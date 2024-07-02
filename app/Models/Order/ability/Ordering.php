<?php

namespace App\Models\Order\ability;

use App\Helper\Ust\UstHelper;
use App\Jobs\Cart\Invoice\AddInvoiceTranslationJob;
use App\Jobs\Cart\Invoice\GenerateCSVJob;
use App\Jobs\Cart\Invoice\GenerateXmlJob;
use App\Models\Cart\Cart;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\InvoiceAddress;
use App\Models\Invoice\InvoiceAddressProduct;
use App\Models\Location\Country;
use App\Models\Order\Order;
use App\Models\Order\OrderAddress;
use App\Models\Order\OrderAddressProduct;
use App\Models\Product\Product;
use App\Models\User\Address;
use Illuminate\Support\Facades\Request;

class Ordering
{

    public static function billingCountry($countryId, $language)
    {
        return Country::select('id', 'customs_price', 'tax_required')
            ->with(['translation' => function ($query) use ($language) {
                $query->select('country_id', 'name')->where('language', $language);
            }])->findOrFail($countryId);
    }

    public static function checkUstId($ustId, $billingCountry)
    {
        if ($billingCountry['tax_required'] == 1 && $billingCountry['has_ust_id'] == 1 && !is_null($ustId)) {
            return UstHelper::checkUst($billingCountry['code'], $ustId);
        } else {
            $ust_status = new \stdClass();
            $ust_status->valid = false;
            return $ust_status;
        }
    }

    public static function orderUstId($ust_status, $request)
    {
        if ($ust_status->valid) {
            return $request['ust_id'];
        }
        return null;
    }

    public static function orderTax($billingCountry, $ust_status, $tax)
    {
        if ($billingCountry['tax_required'] == 0 || $ust_status->valid) {
            $tax = 0;
        }
        return $tax;
    }

    public static function fetchUserAddress($addressId, $userId, $defaultAddress)
    {
        $address = [];
        if (!is_null($addressId)) {
            $address = Address::fetchUserAddress($userId, $addressId);
        } else {
            $address = $defaultAddress;
        }
        return $address;
    }

    public static function generateUniqueIndex($productId, $combinationId, $durationId, $services)
    {
        return $productId . $combinationId . $durationId . $services;
    }

    public static function checkCartItemsStock($item, &$stocksHash, &$stockErrorMessage)
    {
        $stocks = Product::fetchStockAmount($item['product_id'], $item['combination_id']);
        foreach ($stocks as $stock) {
            if (!is_null($stock['stock'])) {
                if (!isset($stocksHash[$stock['option_value_id']])) {
                    $stocksHash[$stock['option_value_id']] = $stock['stock'];
                }
                if ($stocksHash[$stock['option_value_id']] === 0 or $stocksHash[$stock['option_value_id']] < $item['quantity']) {
                    $stockErrorMessage[] = 'Sorry,there is no enough goods for ' . $item['product_title'];
                    break;
                } else {
                    ///subtract the user quantity from available stock
                    $stocksHash[$stock['option_value_id']] = $stocksHash[$stock['option_value_id']] - $item['quantity'];
                }
            }
        }
    }

    public static function taxPrice($taxRate, $price)
    {
        if ($taxRate != 0) {
            return $price * $taxRate;
        }
        return 0;
    }

    public static function discountPrice($discountPrice, $price)
    {
        // return $price * $discountRate;
    }

    public static function workingDayPrice($durationPercent, $price)
    {
        $durationRate = $durationPercent / 100;
        return $price * $durationRate;
    }

    public static function customsPrice($percent, $price)
    {
        if ($percent != 0) {
            return ($price * ($percent / 100));
        }
        return 0;
    }

    public static function prePaidPercent()
    {
        return 2;
    }

    public static function prePaidPrice($price)
    {
        return $price * (self::prePaidPercent() / 100);
    }

    public static function prePaidCouponPrice($price, $percent)
    {
        return $price * ($percent / 100);
    }

    public static function sessionIdGenerator($type)
    {
        return ['id' => uniqid(time()), 'payment_intent' => uniqid(time()), 'payment_type' => $type, 'url' => ''];
    }

    public static function createInvoice($sessionId, $paymentIntent)
    {
        $order = Order::where('session_id', $sessionId)->first();
        $orderAddresses = OrderAddress::where('session_id', $sessionId)->get()->toArray();
        $orderProducts = OrderAddressProduct::where('session_id', $sessionId)->get()->toArray();
        foreach ($orderAddresses as &$ad) {
            unset($ad['session_id']);
            $ad['user_id'] = $order['user_id'];
            $ad['payment_intent'] = $paymentIntent;
        }
        
        foreach ($orderProducts as &$product) {
            unset($product['session_id']);
            $product['user_id'] = $order['user_id'];
            $product['payment_intent'] = $paymentIntent;
        }
       
        $invoice = [
            'payment_intent' => $paymentIntent,
            'payment_type' => $order['payment_type'], 
            'comments' => $order['comments'], 
            'user_id' => $order['user_id'],
            'amount_total' => $order['amount_total'], 
            'has_ust_id' => $order['has_ust_id'],
            'country_id' => $order['country_id'], 
            'ust_id' => $order['ust_id'],
            'coupon_code' => $order['coupon_code'], 
            'coupon_percent' => $order['coupon_percent'],
            'coupon_expires_at' => $order['coupon_expires_at'],
            'address' => $order['address'],
            'additional_address' => $order['additional_address'],
            'postcode' => $order['postcode'],
            'country_name' => $order['country_name'],
            'city' => $order['city'],
            'tax_required' => $order['tax_required']
        ];
            
        Invoice::create($invoice);
        InvoiceAddress::insert($orderAddresses);
        InvoiceAddressProduct::insert($orderProducts);
        ///add invoice translation
        dispatch(new AddInvoiceTranslationJob($paymentIntent));
        dispatch(new GenerateCSVJob($paymentIntent));

        ///delete cart item*************
        Cart::where('session_id', $sessionId)->delete();
    }

    public static function orderCollection($request, $userId, $defaultAddress, $billingCountry)
    {
        return [
            'payment_intent' => '',
            'payment_type' => $request['payment_type'],
            'session_id' => '',
            'expires_at' => '',
            'user_id' => $userId,
            'comments' => $request['comments'],
            'country_id' => $defaultAddress['country_id'],
            'address' => $defaultAddress['address'],
            'additional_address' => $defaultAddress['additional_address'],
            'postcode' => $defaultAddress['postcode'],
            'country_name' => $billingCountry['translation']['name'],
            'city' => $defaultAddress['city'],
            'tax_required' => $billingCountry['tax_required'],
            'coupon_code' => null,
            'coupon_percent' => null,
            'coupon_expires_at' => null];
    }

    public static function orderAddressCollection($orderAddressId, $address, $country, $postMethodDuration)
    {
        return [
            'order_address_id' => $orderAddressId,
            'session_id' => '',
            'address_id' => $address['id'] ?? null,
            'country_id' => $address['country_id'],
            'address' => $address['address'],
            'additional_address' => $address['additional_address'],
            'postcode' => $address['postcode'],
            'country_name' => $country['translation']['name'],
            'city' => $address['city'],
            'customs_percent' => $country['customs_price'],
            'customs_price' => 0,
            'post_id' => $postMethodDuration['post_id'],
            'min_items_total_price' => $postMethodDuration['min_price'],
            'post_price' => $postMethodDuration['price'],
            'items_total_net_price' => 0,
        ];
    }

    public static function orderProductsCollection($cartProduct, $product)
    {
        $cartProduct['product_title'] = $cartProduct['product_title'] . ' - ' . implode("|", $product['options']);
        $cartProduct['product_price'] = $product['product_price'];
        $cartProduct['combination_price'] = $product['real_price'];
        $cartProduct['combination_additional_price'] = $product['additional_price'];
        $cartProduct['list_price'] = $product['product_price'] + $product['real_price'] + $product['additional_price'];
        $cartProduct['net_price'] = 0;
        $cartProduct['tax_price'] = 0;
        $cartProduct['quantity'] = $product['quantity'];
        $cartProduct['duration_id'] = $product['duration_id'];
        $cartProduct['duration_percent'] = $product['duration_price'];
        $cartProduct['duration_price'] = 0;
        $cartProduct['duration'] = $product['duration'];
        $cartProduct['discount_quantity'] = isset($product['discount']['quantity']) ? $product['discount']['quantity'] : 0;
        $cartProduct['discount_percent'] = 0;
        $cartProduct['discount_price'] = isset($product['discount']['percent']) ? $product['discount']['percent'] : 0;
        $cartProduct['pre_paid_percent'] = 0;
        $cartProduct['pre_paid_coupon_price'] = 0;
        $cartProduct['services'] = $product['services'];
        $cartProduct['services_data'] = $product['service_values'];
        $cartProduct['services_total_price'] = 0;
        foreach ($product['service_values'] as $service_value) {
            $cartProduct['services_total_price'] += $service_value['price'];
        }
        $cartProduct['customs_percent'] = 0;
        $cartProduct['customs_price'] = 0;
        $cartProduct['total_price'] = 0;

        return $cartProduct;
    }
}
