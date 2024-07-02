<?php

namespace App\Models\Product\Pivot\Type2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pt2Combination extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'combination', 'price', 'additional_price', 'is_active', 'is_default_price'];

    public function images()
    {
        return $this->hasMany(Pt2CombinationImage::class, 'combination_id');
    }

    public function options() {
        return $this->hasMany(Pt2CombinationPt1Combination::class,'pt2_combination_id');
    }
}
