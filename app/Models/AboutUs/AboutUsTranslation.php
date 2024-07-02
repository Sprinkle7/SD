<?php

namespace App\Models\AboutUs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutUsTranslation extends Model
{
    use HasFactory;
    protected $fillable = [
        'about_us_id',
        'description',
        'language'
    ];
}
