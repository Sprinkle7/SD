<?php

namespace App\Models\Invoice;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAddressProductTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'invoice_address_product_id', 'product_id', 'product_title', 'combination_id', 'options', 'language'
    ];

    protected $casts = [
        'options' => 'array'
    ];

}
