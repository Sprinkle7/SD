<?php


namespace App\Models\User\ability;


use App\Helper\Language\LanguageHelper;
use App\Models\User\Address;

trait AddressQuery
{
    public static function fetchUserAddress($userId, $addressId)
    {
        $language = LanguageHelper::getCacheDefaultLang();
        return Address::with(['country:id,customs_price,tax_required','country.translation' => function ($query) use ($language) {
            $query->select('country_id','name')->where('language', $language);
        }])->where('user_id', $userId)->findOrFail($addressId);
    }

    public static function fetchDefaultAddress()
    {
        $address = auth()->user()->only(
            'first_name',
            'last_name',
            'email',
            'phone',
            'gender',
            'company',
            'address',
            'additional_address',
            'postcode',
            'is_default',
            'city',
            'country_id');
        return $address;
    }
}
