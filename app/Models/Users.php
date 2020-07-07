<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Users extends Authenticatable implements JWTSubject
{
    use Notifiable, HasApiTokens;
    protected $table = 'users';
    protected $hidden = ['password', 'openid'];
    protected $fillable = [
        'name', 'email', 'password', 'openid', 'nickname', 'avatar', 'unionid', 'login_ip', 'login_time', 'type', 'status', 'gender', 'phone', 'student_name', 'parent_phone', 'school_name'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}