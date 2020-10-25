<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/4/24
 * Time: 9:13
 */

namespace app\admin\validate;
use think\Validate;

class Admin extends Validate
{
    protected $rule = [
        'username'  => 'require',
        'password' => 'require'
    ];

    protected $message  =   [
        'username.require'      => '用户名必须',
        'password.require'      => '密码必须',
        'gid.require'=>'用户组必须'
    ];

    public function sceneLogin()
    {
        return $this->only(['username','password'])
            ->append('username', 'require')
            ->append('password', 'require');
    }

    public function scenePassword()
    {
        return $this->only(['password'])
            ->append('id', 'require');
    }

    public function sceneInfo()
    {
        return $this->only(['username'])
            ->append('gid', 'require');
    }
}