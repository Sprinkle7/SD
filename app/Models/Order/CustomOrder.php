<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
class CustomOrder extends Model
{
    protected $table = 'my_orders';
    
    protected $fillable = [
        'order_id',
        'products',
        'summary',
    ];
}
