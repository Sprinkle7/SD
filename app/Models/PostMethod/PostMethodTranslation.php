<?php

namespace App\Models\PostMethod;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostMethodTranslation extends Model
{
    use HasFactory;

    protected $fillable=[
        'post_method_id',
        'title',
        'language',
    ];

    public static function generatePostMethodCollection($request) {
        $post =  [
            'title' => $request['title'],
        ];
        if (isset($request['language']))
            $post['language'] = $request['language'];

        return $post;
    }
}
