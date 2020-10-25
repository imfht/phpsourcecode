<?php

namespace app\admin\model;

use think\Model;

class User extends Model
{
    protected $pk = 'uid';
    protected $schema = [
        'uid'          => 'int',
        'ugid'          => 'int',
        'username'        => 'string',
        'password'        => 'string',
        'avatar'      => 'string',
        'sex'       => 'tinyint',
        'birthday'       => 'int',
        'tel'       => 'string',
        'qq'       => 'string',
        'email'       => 'string',
        'status'       => 'tinyint',
        'identifier'       => 'string',
        'token'       => 'string',
        'salt'       => 'string',
        'skin'       => 'string',
        'create_time' => 'int',
    ];
    
    protected $updateTime = false;
    
    public function group()
    {
        return $this->hasOne('UserGroup', 'id', 'ugid');
    }
}
