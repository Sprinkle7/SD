<?php

namespace App\Models\TechnicalInfo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalInfoTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['technical_info_id', 'title', 'description', 'language'];

    public static function generateCategoryCollection($request)
    {
        $technical = [
            'title' => $request['title'],
            'description' => $request['description'],
        ];
        if (isset($request['language']))
            $technical['language'] = $request['language'];
        return $technical;
    }
}
