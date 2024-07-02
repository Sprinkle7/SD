<?php

namespace App\Models\Product\Pivot\Type2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pt2CombinationPt1Combination extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['pt2_combination_id', 'category_id', 'pt1_combination_id'];

}
