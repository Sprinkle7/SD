<?php

namespace App\Models\ContactUs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUsTranslation extends Model
{
    use HasFactory;
    protected $fillable = [
        'contact_us_id',
        'description',
        'language'
    ];
}
