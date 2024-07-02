<?php

namespace App\Models\Portfolio;

use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    public function images()
    {
        return $this->hasMany(PortfolioImage::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function productTranslation()
    {
        return $this->belongsToMany(ProductTranslation::class, 'portfolio_product', 'portfolio_id', 'product_id', 'id', 'product_id');
    }
}
