<?php

namespace App\Models\Product\Pivot\Type2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pt2CombinationImage extends Model
{
    use HasFactory;

    protected $fillable = ['combination_id', 'path','arrange'];

}
