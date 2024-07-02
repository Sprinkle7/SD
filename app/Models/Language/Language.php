<?php

namespace App\Models\Language;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'title', 'active', 'default'];

    public static function cacheLanguages()
    {
        $langs = Language::where('active', 1)->pluck('title','code');
        LanguageHelper::setCacheAllLang($langs);
    }

    public static function cacheDefaultLanguage($languageCode)
    {
        LanguageHelper::setCacheDefaultLang($languageCode);
    }

    public static function generateLanguageCollection($request)
    {
        return [
            'code' => $request['code'],
            'title' => $request['title'],
        ];
    }
}
