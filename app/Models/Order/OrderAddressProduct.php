<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAddressProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_address_id',
        'session_id',
        'tax',
        'tax_price',
        'product_id',
        'product_title',
        'product_price',
        'combination_id',
        'combination_price',
        'combination_additional_price',
        'quantity',
        'duration_id',
        'duration',
        'duration_percent',
        'duration_price',
        'services',
        'services_total_price',
        'services_data',
        'discount_quantity',
        'discount_percent',
        'discount_price',
        'pre_paid_percent',
        'pre_paid_coupon_price',
        'customs_percent',
        'customs_price',
        'list_price',
        'net_price',
        'total_price',
    ];
}
