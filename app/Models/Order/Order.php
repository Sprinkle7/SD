<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'payment_intent',
        'payment_type',
        'comments',
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
}
