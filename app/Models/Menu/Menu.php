<?php

namespace App\Models\Menu;

use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'level', 'arrange',
        'is_active', 'parent_id',
        'thumbnail_image'
    ];

    public static function generateMenuCollection($request)
    {
        $menu = [
            'level' => $request['level'],
            'is_active' => $request['is_active'],
            'parent_id' => $request['level'] > 1 ? $request['parent_id'] : null,
            'thumbnail_image' => $request['thumbnail_image'],
        ];
        if (isset($request['arrange'])) {
            $menu['arrange'] = $request['arrange'];

        }
        return $menu;
    }

    public function cover_image()
    {
        return $this->hasMany(MenuCoverImage::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function productsTranslation()
    {
        return $this->belongsToMany(ProductTranslation::class, 'menu_product', 'menu_id', 'product_id', 'id', 'product_id');
    }

    public function menuT()
    {
        return $this->hasOne(MenuTranslation::class);
    }

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id', 'id');
    }

}
