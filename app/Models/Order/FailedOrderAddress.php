<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedOrderAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_address_id',
        'session_id',
        'address_id',
        'country_id',
        'country_name',
        'city',
        'address',
        'additional_address',
        'postcode',
        'customs_percent',
        'customs_price',
        'post_id',
        'min_items_total_price',
        'post_price',
        'items_total_net_price',
    ];
}
