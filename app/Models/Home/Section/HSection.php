<?php

namespace App\Models\Home\Section;

use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HSection extends Model
{
    use HasFactory;

    protected $fillable = ['type'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'h_section_product');
    }

    public function productT() {
        return $this->belongsToMany(
            ProductTranslation::class,
            'h_section_product', 'h_section_id', 'product_id', 'id', 'product_id')
            ->withPivot('arrange');
    }

    public function section_info()
    {
        return $this->hasOne(HSectionTranslation::class);
    }

}
