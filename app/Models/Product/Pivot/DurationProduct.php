<?php

namespace App\Models\Product\Pivot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DurationProduct extends Model
{
    use HasFactory;
    protected $table = 'duration_product';
    protected $fillable = ['product_id', 'duration_id', 'price', 'default_value'];

    public static function generateDurationCollection($request)
    {
        return [
            'duration_id' => $request['duration_d'],
            'price' => $request['price'],
        ];
    }
}
