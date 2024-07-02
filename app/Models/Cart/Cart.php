<?php

namespace App\Models\Cart;

use App\Models\Cart\ability\CartQuery;
use App\Models\Duration\Duration;
use App\Models\Product\Combination\Combination;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory, CartQuery;


    protected $fillable = [
        'user_id', 'product_id', 'combination_id', 'duration_id', 'quantity', 'services', 'is_active', 'session_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function combination()
    {
        return $this->belongsTo(Combination::class);
    }

    public function duration() {
        return $this->belongsToMany(Duration::class,'duration_product');
    }
}
