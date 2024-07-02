<?php

namespace App\Models\Product;

use App\Models\Category\Category;
use App\Models\Category\CategoryTranslation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ProductTranslation extends Model
{
    use HasFactory,HasSlug;

    protected $fillable = ['title','slug', 'benefit_desc', 'item_desc','feature_desc', 'language', 'product_id'];

    public static function generateProductCollection($request)
    {
        $project = [
            'title' => $request['title'],
            'benefit_desc' => $request['benefit_desc'],
            'item_desc' => $request['item_desc'],
            'feature_desc' => $request['feature_desc'],
        ];
        if (isset($request['language']))
            $project['language'] = $request['language'];

        return $project;
    }

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->usingLanguage('de')
            ->usingSeparator('-')
            ->saveSlugsTo('slug');
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function product_info() {
        return $this->belongsTo(Product::class,'product_id','id');
    }

//    public function


}
