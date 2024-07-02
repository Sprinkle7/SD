<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'email';
    protected $keyType = 'string';
    protected $fillable = [
        'email', 'token', 'request_count', 'expires_at', 'ban_till'
    ];
}
