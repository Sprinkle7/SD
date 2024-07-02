<?php

namespace App\Models\Invoice;

use App\Models\Duration\DurationTranslation;
use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAddressProduct extends Model
{
    use HasFactory;

    protected $primaryKey = 'invoice_address_product_id';
    protected $fillable = [
        'invoice_address_product_id',
        'order_address_id',
        'payment_intent',
        'user_id',
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
        'number_of_images',
        'is_available',
    ];

    protected $casts = ['services_data'=>'array'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function info()
    {
        return $this->hasOne(InvoiceAddressProductTranslation::class, 'invoice_address_product_id', 'invoice_address_product_id');
    }

    public function images()
    {
        return $this->hasMany(InvoiceProductImage::class, 'invoice_address_product_id', 'invoice_address_product_id');
    }

    public function duration_info()
    {
        return $this->belongsTo(DurationTranslation::class, 'duration_id', 'duration_id');
    }
    
}