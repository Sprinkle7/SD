<?php

namespace App\Models\Bookmark;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['product_id','user_id'];
}
