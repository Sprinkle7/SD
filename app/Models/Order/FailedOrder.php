<?php

namespace App\Models\Order;

use App\Jobs\Cart\ActivateCartItemJob;
use App\Jobs\Cart\Order\IncreaseStockJob;
use App\Models\Cart\Cart;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\This;

class FailedOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'payment_intent',
        'payment_type',
        'user_id',
        'has_ust_id',
        'tax_required',
        'country_id',
        'country_name',
        'city',
        'address',
        'additional_address',
        'postcode',
        'ust_id',
        'expires_at',
        'amount_total',
        'coupon_code',
        'coupon_percent',
        'coupon_expires_at'
    ];

    public static function whenOrderCancelORExpire($paymentIntent)
    {
        $order = Order::where('payment_intent', $paymentIntent)->first();
        if (!is_null($order)) {
            $sessionId = $order['session_id'];
            $orderProducts = OrderAddressProduct::where('session_id', $sessionId)->get()->toArray();
            dispatch(new IncreaseStockJob($orderProducts));
            dispatch(new ActivateCartItemJob($sessionId));

            FailedOrder::create($order->toArray());
            FailedOrderAddress::insert(OrderAddress::where('session_id', $sessionId)->get()->toArray());
            FailedOrderAddressProduct::insert(OrderAddressProduct::where('session_id', $sessionId)->get()->toArray());

            Order::where('session_id', $sessionId)->delete();
            OrderAddress::where('session_id', $sessionId)->delete();
            OrderAddressProduct::where('session_id', $sessionId)->delete();
        }

        ///just check session id to delete or activate
    }
}
