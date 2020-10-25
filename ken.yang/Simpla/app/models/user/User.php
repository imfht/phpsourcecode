<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait,
        RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'remember_token');

    public function roles() {
        return $this->hasOne('UserRoles', 'uid');
    }

    /**
     * 获取用户完整信息和判断是否登陆
     */
    public static function info() {
        if (Auth::user()) {
            $user = User::find(Auth::user()->id);
            $logged_in = true;
        } else {
            $user = '';
            $logged_in = false;
        }
        return array($user, $logged_in);
    }

}
