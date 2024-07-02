<?php

namespace App\Models\Download;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownloadTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    public static function generateDownloadTransCollection($request)
    {
        $collection = [
            'title' => $request['title']
        ];
        return $collection;
    }

}
