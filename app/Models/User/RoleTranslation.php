<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id', 'title', 'language'
    ];

    public static function generateRoleCollection($request)
    {
        $role = [
            'title' => $request['title'],
        ];
        if (isset($request['language'])) {
            $role['language'] = $request['language'];
        }
        return $role;
    }
}
