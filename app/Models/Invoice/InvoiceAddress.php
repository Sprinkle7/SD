<?php

namespace App\Models\Invoice;

use App\Models\Duration\Duration;
use App\Models\Duration\DurationTranslation;
use App\Models\Location\Country;
use App\Models\Location\CountryTranslation;
use App\Models\PostMethod\PostMethodTranslation;
use App\Models\User\Address;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAddress extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_address_id';
    protected $keyType = 'string';
    protected $fillable = [
        'order_address_id',
        'payment_intent',
        'user_id',
        'address_id',
        'country_id',
        'country_name',
        'city',
        'address',
        'additional_address',
        'postcode',
        'customs_price',
        'post_id',
        'min_items_total_price',
        'post_price',
        'items_total_net_price',
        'status',
        'xml_invoice'
    ];


    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(CountryTranslation::class, 'country_id', 'country_id');
    }

    public function duration()
    {
        return $this->belongsTo(DurationTranslation::class, 'duration_id', 'duration_id');
    }

    public function post()
    {
        return $this->belongsTo(PostMethodTranslation::class, 'post_id', 'post_method_id');
    }
}
