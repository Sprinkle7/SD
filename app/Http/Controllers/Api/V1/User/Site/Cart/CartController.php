<?php

namespace App\Http\Controllers\Api\V1\User\Site\Cart;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\Models\Cart\CartSystemMessage;
use App\Helper\Ust\UstHelper;
use App\Http\Controllers\Controller;
use App\Models\Cart\Cart;
use App\Models\Coupon\Coupon;
use App\Models\Discount\Discount;
use App\Models\Product\Combination\Combination;
use App\Models\Product\Pivot\Type1\Pt1Combination;
use App\Models\Product\Pivot\Type2\Pt2Combination;
use App\Models\Product\Product;
use App\Models\Service\ServiceValue;
use Dotenv\Exception\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CartController extends Controller
{
    private $systemMessage;

    public function __construct()
    {
        $this->systemMessage = new CartSystemMessage(LanguageHelper::getAppLanguage(\request()),
            'cart', LanguageHelper::getCacheDefaultLang());
    }

    public function addToCart(Request $request, $productId)
    {
        try {
            $combinationId = $request['combination_id'];
            $durationId = $request['duration_id'];
            $services = 0;
            if ($request['services']) {
                $services = $request['services'];
                sort($services);
                $services = implode(',', $services);
            }

            $product = Product::select('id')->where('is_active', 1)->findOrFail($productId);
            $comb = Combination::with('optionValues')->where('is_active', 1)
                ->findorFail($combinationId);
            $optionId = [];
            foreach ($comb['optionValues'] as $value) {
                $optionId[] = $value['id'];
            }
            $optionsMeta = DB::table('option_value_product')->where('product_id', $productId)
                ->whereIn('option_value_id', $optionId)->get();
           
            foreach ($optionsMeta as $stock) {
                if ($stock->stock === 0) {
                    throw new BadRequestException($this->systemMessage->notEnoughStock());
                }
            }
            
            $userId = auth()->user()->id;

            $cart = Cart::where('user_id', $userId)->where('product_id', $productId)
                ->where('combination_id', $combinationId)->where('duration_id', $durationId)
                ->where('services', $services)->first();

            if (is_null($cart)) {
                Cart::create(['user_id' => $userId, 'product_id' => $productId, 'type' => $product['type'],
                    'combination_id' => $combinationId, 'duration_id' => $durationId,
                    'quantity' => $request['quantity'],
                    'services' => $services]);
            } else {
                Cart::where('user_id', $userId)->where('product_id', $productId)
                    ->where('combination_id', $combinationId)->where('duration_id', $durationId)
                    ->where('services', $services)
                    ->update(['quantity' => $request['quantity']]);
            }
            return Response::response200([$this->systemMessage->addToCart()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (BadRequestException $exception) {
            return Response::error400($exception->getMessage());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function removeFromCart(Request $request, $productId, $combinationId, $durationsId)
    {
        try {
            $userId = auth()->user()->id;
            $services = 0;

            if ($request['services']) {
                $services = explode(',', $request['services']);
                sort($services);
                $services = implode(',', $services);
            }

            Cart::where('user_id', $userId)->where('product_id', $productId)
                ->where('combination_id', $combinationId)->where('duration_id', $durationsId)
                ->where('services', $services)->delete();
            return Response::response200([$this->systemMessage->removeFromCart()]);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchCartItems(Request $request)
    {
        try {
            $userId = auth()->user()->id;
            $language = LanguageHelper::getAppLanguage($request);
            $cartItems = Cart::fetchCartItems($userId, $language, $request['page'], $request['per_page']);
            return Response::response200([], $cartItems);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($exception->getMessage());
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function fetchDiscount(Request $request, $productId)
    {
        try {

            $discount = Discount::where('product_id', $productId)
                ->where('quantity', '<=', $request['quantity'])
                ->orderBy('quantity', 'DESC')->first();

            return Response::response200([], $discount);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function cartItemCount()
    {
        try {
            $userId = auth()->user()->id;
            $cartItem['count'] = Cart::where('user_id', $userId)->where('is_active', 1)->sum('quantity');
            return Response::response200([], $cartItem);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function calculatePrice(Request $request)
    {
        try {
            ///not in use
            $taxRate = $request['tax'] / 100;
            $totalProductPrice = 0;
            foreach ($request['items'] as $product) {
                $productPrice = $product['price'] + $product['additional_price'];
                if (!is_null($product['discount'])) {
                    $productPrice = $productPrice - ($productPrice * ($product['discount'] / 100));
                }
                $productPrice = $productPrice + $product['duration_price'];
                $productPrice = $productPrice * $product['quantity'];
                $productPrice = $productPrice + ($productPrice * $taxRate);
                $totalProductPrice = $totalProductPrice + $productPrice;
            }
            $totalShippingPrice = 0;
            $total = 0;
            if (isset($request['shipping'])) {
                foreach ($request['shipping'] as $shipping) {
                    $totalShippingPrice = $totalShippingPrice + $shipping['custom_price'] + $shipping['post_price'];
                }
                $total = $totalProductPrice + $totalShippingPrice;

            } else {
                $total = $totalProductPrice;

            }
            $priceInfo = [
                'product_total_price' => substr_replace($totalProductPrice, '', strpos($totalProductPrice, '.') + 3),
                'shipping_total_price' => substr_replace($totalShippingPrice, '', strpos($totalShippingPrice, '.') + 3),
                'total' => substr_replace($total, '', strpos($total, '.') + 3),];
            return Response::response200([], $priceInfo);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function checkStockCount(Request $request, $product_id)
    {
        try {
            return Response::response200([], Cart::checkStock($product_id, $request['combinations']));
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (ValidationException $exception) {
            return Response::error400(explode('||', $exception->getMessage()));
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function checkCoupon(Request $request)
    {
        try {
            $coupon = Coupon::where('code', $request['code'])->where('expires_at', '>', now())->firstOrFail();
            return Response::response200([], $coupon);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (ValidationException $exception) {
            return Response::error400(explode('||', $exception->getMessage()));
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }

    public function checkUstId(Request $request)
    {
        try {
            $result = UstHelper::checkUst($request['country_code'], $request['ust_id']);
            return Response::response200([], $result);
        } catch (ModelNotFoundException $exception) {
            return Response::error404($this->systemMessage->error404());
        } catch (ValidationException $exception) {
            return Response::error400(explode('||', $exception->getMessage()));
        } catch (\Exception $exception) {
            return Response::error500($this->systemMessage->error500());
        }
    }
}
