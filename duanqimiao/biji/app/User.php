<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function books(){
        return $this->hasMany('App\Book');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bijis(){
        return $this->hasMany('App\Biji');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function signs(){
        return $this->hasOne('App\Sign');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(){
        return $this->hasMany('App\Comment');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function collects(){
        return $this->hasMany('App\Collect');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goods(){
        return $this->hasMany('App\Good');
    }
}
