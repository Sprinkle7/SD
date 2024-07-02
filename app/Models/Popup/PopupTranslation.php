<?php

namespace App\Models\Popup;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopupTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'popup_id', 'title', 'content', 'language'
    ];

    public static function generateCollection($request)
    {
        $collection = [
            'title' => $request['title'],
            'content' => $request['content'],
        ];
        if (isset($request['language'])) {
            $collection['language'] = $request['language'];
        }

        return $collection;
    }

    public function popupInfo()
    {
        return $this->belongsTo(Popup::class,'popup_id','id');
    }
}
