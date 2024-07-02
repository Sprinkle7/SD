<?php


namespace App\Models\Location\ability;


use App\Models\Location\Country;
use App\Models\Location\CountryPostDuration;

trait CountryQuery
{
    public static function fetchCountryWithTranslation($countryId, $language)
    {
        return Country::with(['translation' => function ($query) use ($language) {
            $query->where('language', $language);
        }])->findOrFail($countryId);
    }

    public static function fetchPostDurationWithTranslation($countryId,$postDurationId, $language) {
        return CountryPostDuration::with(['postMethod' => function ($query) use ($language) {
            $query->where('language', $language);
        }, 'duration' => function ($query) use ($language) {
            $query->where('language', $language);
        }])->where('country_id', $countryId)
            ->findOrFail($postDurationId);
    }
}
