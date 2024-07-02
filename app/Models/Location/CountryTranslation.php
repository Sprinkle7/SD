<?php

namespace App\Models\Location;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
        'info',
        'language'
    ];

    public static function generateCountryTranCollection($request)
    {
        $country = [
            'name' => $request['name'],
            'info' => $request['info'],
        ];

        if (isset($request['language']))
            $country['language'] = $request['language'];

        return $country;
    }

    public function country_info()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
