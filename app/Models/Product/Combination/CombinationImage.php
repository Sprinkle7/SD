<?php

namespace App\Models\Product\Combination;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CombinationImage extends Model
{
    use HasFactory;

    protected $fillable = ['combination_id', 'path','arrange'];

}
