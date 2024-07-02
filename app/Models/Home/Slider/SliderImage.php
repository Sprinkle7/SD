<?php

namespace App\Models\Home\Slider;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SliderImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'slider_id', 'path', 'mobile_path', 'link', 'language', 'sorting'];
}
