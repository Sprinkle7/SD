<?php

namespace App\Models\Home\Section;

use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HSectionTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['h_section_id', 'title', 'language'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'h_section_product', 'h_section_id', 'product_id', 'h_section_id', 'id');
    }

    public function productT() {
        return $this->belongsToMany(ProductTranslation::class,
            'h_section_product', 'h_section_id', 'product_id', 'h_section_id', 'product_id')
            ->withPivot('arrange');
    }

    public function sectionInfo()
    {
        return $this->belongsTo(HSection::class,'h_section_id','id');
    }
}
