<?php

namespace App\Models\Page;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageSidebarInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title'
    ];
}
