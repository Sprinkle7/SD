<?php

namespace App\Models\User;

use App\Models\Location\Country;
use App\Models\Location\CountryTranslation;
use App\Models\User\ability\AddressQuery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory, AddressQuery;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'company',
        'address',
        'additional_address',
        'postcode',
        'city',
        'country_id',
        'is_default',
        'user_id',
    ];

    public static function generateAddressCollection($request)
    {
        $user = [
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'gender' => $request['gender'],
            'company' => $request['company'],
            'address' => $request['address'],
            'is_default' => 1,
            'additional_address' => isset($request['additional_address']) ? $request['additional_address'] : null,
            'postcode' => $request['postcode'],
            'city' => $request['city'],
            'country_id' => $request['country_id'],
        ];

        return $user;
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
