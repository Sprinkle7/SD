<?php

namespace App\Models\Duration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DurationTranslation extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'language', 'duration_id'];

    public static function generateDurationCollection($request)
    {
        $duration = [
            'title' => $request['title'],
        ];
        if (isset($request['language']))
            $duration['language'] = $request['language'];

        return $duration;
    }

    public function duration_info()
    {
        return $this->belongsTo(Duration::class, 'duration_id');
    }
}
