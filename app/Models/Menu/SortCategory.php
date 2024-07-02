<?php

namespace App\Models\Menu;

use Illuminate\Database\Eloquent\Model;

class SortCategory extends Model
{

    protected $fillable = [
        'category', 'product_id'
    ];

}
