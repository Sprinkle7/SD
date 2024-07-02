<?php

namespace App\Models\Duration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Duration extends Model
{
    use HasFactory;

    protected $fillable = ['duration'];

    public static function generateDurationCollection($request)
    {
        return $duration = [
            'duration' => $request['duration'],
        ];
    }

    public function durationTranslation() {
        return $this->hasOne(DurationTranslation::class);
    }

    public function durationTranslations() {
        return $this->hasMany(DurationTranslation::class);
    }
}
