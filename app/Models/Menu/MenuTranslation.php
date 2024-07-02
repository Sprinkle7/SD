<?php

namespace App\Models\Menu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class MenuTranslation extends Model
{
    use HasFactory,HasSlug;

    protected $fillable = [
        'menu_id', 'title',
        'description','lower_description', 'language'
    ];

    public static function generateMenuTransCollection($request)
    {
        $menu = [
            'title' => $request['title'],
            'description' => $request['description'],
            'lower_description' => $request['lower_description'],
        ];

        if (isset($request['language']))
            $menu['language'] = $request['language'];
        return $menu;
    }

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->usingLanguage('de')
            ->usingSeparator('-')
            ->saveSlugsTo('slug');
    }

    public function menu_info()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function cover_image()
    {
        return $this->hasMany(MenuCoverImage::class,'menu_id','menu_id');
    }
}
