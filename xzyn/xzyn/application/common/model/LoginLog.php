<?php
namespace app\common\model;

use think\Model;

class LoginLog extends Model
{
    public function User()
    {
        return $this->hasOne('User', 'id', 'uid')->field('username, name');
    }
}