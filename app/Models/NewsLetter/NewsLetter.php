<?php

namespace App\Models\NewsLetter;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsLetter extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'email', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
