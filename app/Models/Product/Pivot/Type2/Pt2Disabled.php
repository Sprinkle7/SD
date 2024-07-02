<?php

namespace App\Models\Product\Pivot\Type2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pt2Disabled extends Model
{
    use HasFactory;

    protected $fillable = ['pt2_id', 'category_id', 'pt1_combination_id', 'disabled_category_id', 'disabled_pt1_combination_id'];

}
