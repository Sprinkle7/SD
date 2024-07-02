<?php

namespace App\Models\Option;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['option_id', 'title', 'language'];

    public static function generateOptionTransCollection($request)
    {
        $optTranslate = [
            'title' => $request['title'],
        ];
        if (isset($request['language']))
            $optTranslate['language'] = $request['language'];

        return $optTranslate;
    }

    public function optionValues() {
        return $this->hasMany(OptionValueTranslation::class,'option_id','option_id');
    }
}
