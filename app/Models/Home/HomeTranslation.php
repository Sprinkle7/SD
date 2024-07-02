<?php

namespace App\Models\Home;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'home_id',
        'description',
        'language'
    ];

    public function home_info()
    {
        return $this->belongsTo(Home::class,'home_id','id');
    }
}
