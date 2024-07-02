<?php

namespace App\Models\Product\Pivot\Type2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pt2Pt1Combination extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = ['pt2_id', 'category_id', 'pt1_id', 'pt1_combination_id', 'arrange'];

}
