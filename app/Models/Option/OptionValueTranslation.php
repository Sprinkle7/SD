<?php

namespace App\Models\Option;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionValueTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['option_id', 'option_value_id', 'title', 'language'];

    public static function generateOptionTransCollection($request)
    {
        $optValTranslate = [
            'title' => $request['title'],
        ];
        if (isset($request['language']))
            $optValTranslate['language'] = $request['language'];

        return $optValTranslate;
    }
}
