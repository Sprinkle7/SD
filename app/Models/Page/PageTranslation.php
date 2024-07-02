<?php

namespace App\Models\Page;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id', 'title', 'slug', 'content','section', 'language','footer_alias',
    ];

    protected $casts = [
        'section'=>'array'
    ];

    public static function generateCollection($request)
    {
        $collection = [
            'title' => $request['title'],
            'slug' => $request['slug'],
            'content' => $request['content'],
            'footer_alias' => $request['footer_alias'],
            'section' => $request['section'],
        ];
        if (isset($request['language']))
            $collection['language'] = $request['language'];

        return $collection;
    }

    public function pageInfo()
    {
        return $this->belongsTo(Page::class,'page_id','id');
    }
}
