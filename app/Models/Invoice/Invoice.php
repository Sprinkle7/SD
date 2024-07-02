<?php

namespace App\Models\Invoice;

use App\Models\Invoice\ability\InvoiceQuery;
use App\Models\Location\CountryTranslation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory, InvoiceQuery;

    protected $primaryKey = 'payment_intent';
    public $incrementing = false;

    protected $fillable = [
        'payment_intent',
        'payment_type',
        'comments',
        'user_id',
        'amount_total',
        'is_complete',
        'country_id',
        'country_name',
        'city',
        'address',
        'additional_address',
        'postcode',
        'has_ust_id',
        'ust_id',
        'expires_at',
        'tax_required',
        'coupon_code',
        'coupon_percent',
        'coupon_expires_at',
        'tax_required',
        'seen',
        'state',
        'confirmed_email_has_sent',
        'confirmed_email_sent_at',
        'file'
    ];

    public function addresses()
    {
        return $this->hasMany(InvoiceAddress::class, 'payment_intent', 'payment_intent');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function billing_country()
    {
        return $this->belongsTo(CountryTranslation::class, 'country_id', 'country_id');
    }
}
