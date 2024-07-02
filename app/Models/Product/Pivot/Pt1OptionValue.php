<?php

namespace App\Models\Product\Pivot;

use App\Models\Product\ability\ProductQuery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pt1OptionValue extends Model
{
    use HasFactory, ProductQuery;

    protected $table = 'pt1_option_value';
    public $timestamps = false;
    protected $fillable = [
        'product_id',
        'option_id',
        'option_value_id'
    ];

    public static function generateCollection($request)
    {
        return [
            'option_id' => $request['option_id'],
            'option_value_id' => $request['option_value_id'],
        ];
    }

}
