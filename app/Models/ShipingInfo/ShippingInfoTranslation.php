<?php

namespace App\Models\ShipingInfo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingInfoTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['shipping_info_id', 'title', 'description', 'language'];

    public static function generateCategoryCollection($request)
    {
        $shipping = [
            'title' => $request['title'],
            'description' => $request['description'],
        ];
        if (isset($request['language']))
            $shipping['language'] = $request['language'];
        return $shipping;
    }
}
