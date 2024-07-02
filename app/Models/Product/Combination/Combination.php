<?php

namespace App\Models\Product\Combination;

use App\Models\Option\OptionValue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Combination extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'combination',
        'price', 'real_price', 'additional_price',
        'is_active', 'is_default'];

    public function images()
    {
        return $this->hasMany(CombinationImage::class, 'combination_id');
    }

    public function optionValues() {
        return $this->belongsToMany(
            OptionValue::class,
            'combination_option_value');
    }

}
