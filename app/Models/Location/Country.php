<?php

namespace App\Models\Location;

use App\Models\Location\ability\CountryQuery;
use App\Models\PostMethod\PostMethodTranslation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory, CountryQuery;

    protected $fillable = [
        'has_ust_id',
        'customs_price',
        'is_active',
        'tax_required',
        'code',
    ];

    public static function generateCountryCollection($request)
    {
        return [
            'has_ust_id' => $request['has_ust_id'],
            'customs_price' => $request['customs_price'],
            'tax_required' => $request['tax_required'],
            'code' => $request['code'],
        ];
    }

    public function translation()
    {
        return $this->hasOne(CountryTranslation::class);
    }

    public function postMethod()
    {
        return $this->belongsToMany(PostMethodTranslation::class, 'country_post_duration','country_id' ,'post_id', 'id', 'post_method_id');
    }
}
