<?php

namespace App\Http\Controllers\Api\V1\User\Site\Cart;

use App\Helper\Language\LanguageHelper;
use App\Helper\Payment\PayPalHelper;
use App\Helper\Payment\StripeHelper;
use App\Helper\Response\Response;
use App\Helper\Setting\Tax;
use App\Helper\SystemMessage\Models\Order\OrderSystemMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\Site\Order\CreateOrderRequest;
use App\Jobs\Cart\ActivateCartItemJob;
use App\Jobs\Cart\Order\CreateOrderJob;
use App\Jobs\Cart\Order\IncreaseStockJob;
use App\Models\Cart\Cart;
use App\Models\Coupon\Coupon;
use App\Models\Location\CountryPostDuration;
use App\Models\Order\ability\Ordering;
use App\Models\Order\FailedOrder;
use App\Models\Order\Order;
use App\Models\Order\OrderAddress;
use App\Models\Order\OrderAddressProduct;
use App\Models\User;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Stripe\Exception\SignatureVerificationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class OrderController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new OrderSystemMessage(LanguageHelper::getAppLanguage(\request()),
            'order', LanguageHelper::getCacheDefaultLang());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * fetch tax, user id
     * fetch each address and fetch country and post method based on address
     * add customs price, post price
     * fetch cart's products and fetch product price, additional price and working day(duration) price
     * add product prices together and add tax price if has not ust id
     */
    
    public function createOrder(CreateOrderRequest $request)
    {
        try {
            $myproducts = $request['myproducts'];
            $summary = $request['summary'];
            $totalPriceForPayment = $request['summary']['totalNetPrice'] + $request['summary']['custom'];
            // $language = LanguageHelper::getCacheDefaultLang();
            $language = 'de';
            $tax = Tax::fetch()['tax'];
            $user = auth()->user();
            $userId = $user->id;
            /** the user profiles should be complete because this address is use to calcuate the bulling address **/
            User::ProfileIsComplete($user);
            /** important prices in calculation process **/
            $countriesCustomsTotalPrice = 0;
            $postsTotalPrice = 0;
            $productsTotalPrice = 0;
            /** address and product of each address **/
            $orderAddresses = [];
            $productsOrder = [];
            /** user address that use as billing address **/
            $defaultAddress = $user;
            $defaultAddress['id'] = null;
            $defaultAddress['user_id'] = $userId;
            /** fetch billing address country **/
            $billingCountry = Ordering::billingCountry($defaultAddress['country_id'], $language);
            $defaultAddress['country'] = $billingCountry;
            // $request['payment_type'] = 'paypal'; test mode
           
            /** generate orderCollection **/
            $order = Ordering::orderCollection($request, $userId, $defaultAddress, $billingCountry);
            /** check use ust id is valid or not **/
            if(!empty($request['ust_id'])) {
                $order['ust_id'] = $request['ust_id'];
                $order['has_ust_id'] = true;
                $ust_status = new \stdClass();
                $ust_status->valid = true;
            } else {
                $order['ust_id'] = '';
                $order['has_ust_id'] = false;
                $ust_status = new \stdClass();
                $ust_status->valid = false;
            }
            // $ust_status->valid = true; test mode
            /** check if order has tax or not **/
            $tax = Ordering::orderTax($billingCountry, $ust_status, $tax);
            $taxRate = $tax / 100;
            /** fetch available cart item to compare to user request **/
            $products = Cart::fetchCartItems($userId, $language, 1, 50, 0);
            if (count($products) <= 0) {
                throw new ModelNotFoundException($this->systemMessage->error404());
            }
            $stockErrorMessage = [];
            $stocksHash = [];
            $cartProducts = [];
            $address = [];
            
            /**
             * this loops check the order request and generate address collection form each address
             * and check item's stock of each address
             */
            foreach ($request['addresses'] as $add) {
                /**fetch products address**/
                if (!isset($addresses[$add['address_id']])) {
                    $addresses[$add['address_id']]
                        = Ordering::fetchUserAddress($add['address_id'], $order['user_id'], $defaultAddress);
                }
                $orderAddressId = uniqid() . uniqid() . Carbon::now()->timestamp;
                $postMethodDuration = CountryPostDuration::findOrFail($add['post_duration_id']);
                $orderAddresses[$orderAddressId] =
                    Ordering::orderAddressCollection($orderAddressId, $addresses[$add['address_id']], $addresses[$add['address_id']]['country'], $postMethodDuration);
                ///generate order product collection
                foreach ($add['items'] as $index => $item) {
                    $productsOrder[$index] = $item;
                    $productsOrder[$index]['order_address_id'] = $orderAddressId;
                    $productsOrder[$index]['session_id'] = '';
                    $productsOrder[$index]['tax'] = $tax;
                    Ordering::checkCartItemsStock($item, $stocksHash, $stockErrorMessage);
                }
            }
            
            /**
             * if the there is no enough stock for return validation error
             */
            if (count($stockErrorMessage) > 0) {
                throw new ValidationException(implode('||', $stockErrorMessage));
            }

            if (!is_null($request['coupon'])) {
                $coupon = Coupon::where('code', $request['coupon'])->where('expires_at', '>=', now())->first();
                if (isset($coupon['percent'])) {
                    $order['coupon_code'] = $coupon['code'];
                    $order['coupon_percent'] = $coupon['percent'];
                    $order['coupon_expires_at'] = $coupon['expires_at'];
                }
            }

            /**
             * this loop generate each product collection from each item and calculate
             * product price, service price, working day price, discounts, customs price and post price
             */

            foreach ($products as $index => $product) {
                $itemIndex = Ordering::generateUniqueIndex($product['product_id'], $product['combination_id'],
                    $product['duration_id'], $product['services']);
                $cartProducts[$itemIndex] = $productsOrder[$itemIndex];
                $cartProducts[$itemIndex] = Ordering::orderProductsCollection($cartProducts[$itemIndex], $product);
                $productNetPrice = $cartProducts[$itemIndex]['list_price'];

                ///bundle discount
                $productNetPrice -= $cartProducts[$itemIndex]['discount_price'];

                ///working day price
                $durationPrice = Ordering::workingDayPrice($cartProducts[$itemIndex]['duration_percent'], $productNetPrice);
                $productNetPrice += $durationPrice;
                $cartProducts[$itemIndex]['duration_price'] = $durationPrice;
                ///service values
                $productNetPrice = $productNetPrice + $cartProducts[$itemIndex]['services_total_price'];
                ///product net price
                $productNetPrice = $productNetPrice * $cartProducts[$itemIndex]['quantity'];
                ///pre paid discount
                $discountPercent = 0;
                if ($request['payment_type'] == 'prePaid' || $request['payment_type'] == 'postPaid' ) {
                    $discountPercent += Ordering::prePaidPercent();
                    $cartProducts[$itemIndex]['pre_paid_percent'] = Ordering::prePaidPercent();
                }

                if (!is_null($order['coupon_percent'])) {
                        $discountPercent += $coupon['percent'];
                }

                $prepaidCouponPrice = Ordering::prePaidCouponPrice($productNetPrice, $discountPercent);
                $productNetPrice -= $prepaidCouponPrice;
                $cartProducts[$itemIndex]['pre_paid_coupon_price'] = $prepaidCouponPrice;

                $cartProducts[$itemIndex]['net_price'] = $productNetPrice;
                ///compute total list price for each product
                $orderAddresses[$productsOrder[$itemIndex]['order_address_id']]['items_total_net_price'] += $productNetPrice;
                if ($orderAddresses[$productsOrder[$itemIndex]['order_address_id']]['items_total_net_price'] >
                    $orderAddresses[$productsOrder[$itemIndex]['order_address_id']]['min_items_total_price']) {
                    $orderAddresses[$productsOrder[$itemIndex]['order_address_id']]['post_price'] = 0;
                }
                ///each address customs price base on list price
                $totalPrice = 0;
                $customsPercent = $orderAddresses[$productsOrder[$itemIndex]['order_address_id']]['customs_percent'];
                if ($customsPercent != 0) {
                    $customs = Ordering::customsPrice($customsPercent, $productNetPrice);
                    $orderAddresses[$productsOrder[$itemIndex]['order_address_id']]['customs_price'] += $customs;
                    $totalPrice = $productNetPrice + $customs;
                    $cartProducts[$itemIndex]['customs_percent'] = $customsPercent;
                    $cartProducts[$itemIndex]['customs_price'] = $customs;
                    $cartProducts[$itemIndex]['tax'] = 0;
                } else {
                    $taxPrice = Ordering::taxPrice($taxRate, $productNetPrice);
                    $totalPrice = $productNetPrice + $taxPrice;
                    $cartProducts[$itemIndex]['tax_price'] = $taxPrice;
                }
                $cartProducts[$itemIndex]['total_price'] = $totalPrice;
                $productsTotalPrice += $totalPrice;
            }

            foreach ($orderAddresses as $address) {
                $postsTotalPrice += $address['post_price'];
            }

            $totalPrice = $productsTotalPrice + $postsTotalPrice;
            $order['amount_total'] = $totalPriceForPayment;

            $session = [];
            //  $session = ['id' => 'sdfsdf', 'payment_intent' => 'kjsf'];
            if ($request['payment_type'] == 'stripe') {
                $session = StripeHelper::createSession(
                    [StripeHelper::orderDataModel($totalPriceForPayment, 'Total Price', 1)]);
                $session['payment_type'] = 'Kreditkarte';
            } else if ($request['payment_type'] == 'paypal') {
                $paypal = PayPalHelper::createOrder(round($totalPriceForPayment, 2));
                $session['id'] = $paypal->result->id;
                $session['payment_intent'] = $paypal->result->id;
                $session['payment_type'] = 'paypal';
                foreach ($paypal->result->links as $link) {
                    if ($link->rel == 'approve') {
                        $session['url'] = $link->href;
                    }
                }
                $session['payment_type'] = 'PayPal';
            } else if ($request['payment_type'] == 'prePaid') {
                $session = Ordering::sessionIdGenerator('prePaid');
                $session['payment_type'] = 'Vorkasse (2% Rabatt)';
            } else if ($request['payment_type'] == 'postPaid') {
                $session = Ordering::sessionIdGenerator('postPaid');
                $session['payment_type'] = 'Rechnungskauf';
            } else {
                throw new BadRequestException('payment is not available');
            }
            //job to save order, order Address and order address product, reduce stuck,deactivate cart items
            dispatch(new CreateOrderJob($session, $order, $orderAddresses, $cartProducts,$myproducts, $summary));
            $data = ['id' => $session['id'], 'url' => $session['url'], 'payment_type' => $session['payment_type']];
            return Response::response200([$this->systemMessage->create()], $data);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (ValidationException $exception) {
            return Response::error400(explode('||', $exception->getMessage()));
        } catch (\Exception $exception) {
            return Response::error500($exception->getMessage());
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function webhook(Request $request)
    {
        try {
            $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
            $payload = file_get_contents('php://input');
            $data = $request->all();
            Storage::disk('local')->put('file.txt', json_encode($data['data']['object']));
            $event = StripeHelper::verifySession($payload, $sig_header);

            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    $paymentIntent = $session['payment_intent'];
                    $sessionId = $session['id'];
                    $payment = StripeHelper::retrievePaymentIntent($paymentIntent);

                    if ($payment['status'] == 'succeeded') {
                        Ordering::createInvoice($sessionId, $paymentIntent);
                    } else {
                        $orderProducts = OrderAddressProduct::where('session_id', $sessionId)->get()->toArray();
                        dispatch(new IncreaseStockJob($orderProducts));
                        dispatch(new ActivateCartItemJob($sessionId));
                    }
                    Order::where('session_id', $sessionId)->delete();
                    OrderAddress::where('session_id', $sessionId)->delete();
                    OrderAddressProduct::where('session_id', $sessionId)->delete();
                    break;
                case 'payment_intent.canceled':
                    $paymentIntent = $event->data->object;
                    $paymentIntentId = $paymentIntent['id'];
                    FailedOrder::whenOrderCancelORExpire($paymentIntentId);
                    break;
                case 'checkout.session.async_payment_succeeded':
                //  Storage::disk('local')->put('success.txt', '22222222');
                    break;
                case 'checkout.session.async_payment_failed':
                //  Storage::disk('local')->put('fail.txt', '333333333');
                    break;
            }

            return Response::response200();

        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\UnexpectedValueException $exception) {
            return Response::error400([]);
        } catch (SignatureVerificationException $exception) {
            return Response::error400($exception->getMessage());
        } catch (\Exception $exception) {
            return Response::error500($exception->getMessage());
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function cancelStripeOrder(Request $request)
    {
        try {
            $userId = auth()->user()->id;
            $order = Order::where('user_id', $userId)->where('session_id', $request['session_id'])->firstOrFail();
            try {
                StripeHelper::cancelPaymentIntent($order['session_id']);
                Cart::where('session_id', $order['session_id'])->update(['session_id' => null, 'is_active' => 1]);
                FailedOrder::whenOrderCancelORExpire($order['payment_intent']);
                return Response::response200([$this->systemMessage->orderCanceled()]);
            } catch (\Exception $exception) {
                Cart::where('session_id', $order['session_id'])->update(['session_id' => null, 'is_active' => 1]);
                FailedOrder::whenOrderCancelORExpire($order['payment_intent']);
                return Response::response200([$this->systemMessage->orderCanceled()]);
            }
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }


    public function capturePayPalOrder($token)
    {
        try {
            $capture = PayPalHelper::captureOrder($token);
            if ($capture->result->status == 'COMPLETED') {
                Ordering::createInvoice($capture->result->id, $capture->result->id);
                Order::where('session_id', $capture->result->id)->delete();
                OrderAddress::where('session_id', $capture->result->id)->delete();
                OrderAddressProduct::where('session_id', $capture->result->id)->delete();
            }
            return Response::response200([]);
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function cancelPayPalOrder($token)
    {
        try {
            $userId = auth()->user()->id;
            $order = Order::where('user_id', $userId)->where('session_id', $token)->firstOrFail();
            try {
                PayPalHelper::cancelOrder($order['session_id']);
                Cart::where('session_id', $order['session_id'])->update(['session_id' => null, 'is_active' => 1]);
                FailedOrder::whenOrderCancelORExpire($order['session_id']);
                return Response::response200([], $this->systemMessage->orderCanceled());
            } catch (\Exception $exception) {
                Cart::where('session_id', $order['session_id'])->update(['session_id' => null, 'is_active' => 1]);
                FailedOrder::whenOrderCancelORExpire($order['session_id']);
                return Response::response200([$this->systemMessage->orderCanceled()]);
            }
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

}
