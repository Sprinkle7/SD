<?php

namespace App\Models\Home\Slider;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    public function images() {
        return $this->hasMany(SliderImage::class);
    }
}
