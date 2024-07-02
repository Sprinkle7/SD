<?php

namespace App\Models\Menu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuCoverImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id', 'path', 'mobile_path', 'link', 'language'
    ];


}
