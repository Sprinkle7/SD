<?php

namespace App\Models;

use App\Helper\Language\LanguageHelper;
use App\Helper\SystemMessage\Models\Auth\AuthSystemMessage;
use App\Models\NewsLetter\NewsLetter;
use App\Models\Product\Product;
use App\Models\Product\ProductTranslation;
use App\Models\User\Role;
use App\Models\User\RoleTranslation;
use Dotenv\Exception\ValidationException;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'password',
        'company',
        'address',
        'additional_address',
        'postcode',
        'city',
        'country_id',
        'role_id',
        'profile_completed'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function generateUserCollection($request)
    {
        $user = [
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'gender' => $request['gender'],
            'password' => bcrypt($request['password']),
            'company' => $request['company'],
            'address' => $request['address'],
            'additional_address' => isset($request['additional_address']) ? $request['additional_address'] : null,
            'postcode' => $request['postcode'],
            'city' => $request['city'],
            'country_id' => $request['country_id'],
            'is_default' => 1,
            'role_id' => $request['role_id'],
        ];

        if (isset($request['email'])) {
            $user['email'] = $request['email'];
        }
        if (isset($request['phone'])) {
            $user['phone'] = $request['phone'];
        }

        return $user;
    }

    public static function ProfileIsComplete($user)
    {
        $systemMessage = new AuthSystemMessage(LanguageHelper::getAppLanguage(\request()), 'profile', LanguageHelper::getCacheDefaultLang());
        if ($user->profile_completed == 0) {
            throw new ValidationException($systemMessage->profileNotCompleted());
        }
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function RoleTranslation()
    {
        return $this->belongsTo(RoleTranslation::class, 'role_id', 'role_id');
    }

    public function bookmarks()
    {
        return $this->belongsToMany(ProductTranslation::class, 'bookmarks', 'user_id', 'product_id', 'id', 'product_id');
    }

    public function bookmarkP()
    {
        return $this->belongsToMany(Product::class, 'bookmarks');
    }

    public function newsletter()
    {
        return $this->hasOne(NewsLetter::class);
    }
}
