<?php

namespace App\Models\Language;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LanguageReference extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'code'];
}
