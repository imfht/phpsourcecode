<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/4/24
 * Time: 9:13
 */

namespace app\first\validate;
use think\Validate;

class Member extends Validate
{
    protected $rule = [
        'username'  => 'require|min:6|max:18|alphaDash',
        'password' => 'require',
        'email' => 'require|email'
    ];

    protected $message  =   [
//        'phone.require'          => '手机号必须',
//        'phone'                    => '手机号格式错误',
//        'password.require'      => '密码必须',
//        'email.require'         => '邮箱格必须',
//        'email'                  => '邮箱格式错误',
    ];

    // edit 验证场景定义
    public function sceneEdit()
    {
        return $this->only(['username','email','tel'])
            ->append('username', 'require|min:6|max:18|alphaDash')
            ->append('email', 'require|email')
            ->append('tel', ['require','regex' => '/^1[34578]\d{9}$/']);
    }

    public function sceneDelete()
    {
        return $this->only(['id'])
            ->append('id', 'require|integer');
    }

    public function sceneInfo()
    {
        return $this->only(['id','info'])
            ->append('id', 'require')
            ->append('info', 'require');
    }

    public function scenePassword(){
        return $this->only(['id','password'])
            ->append('id', 'require')
            ->append('password', 'require');
    }

    public function sceneLogin()
    {
        return $this->only(['username','password'])
            ->append('username', 'require')
            ->append('password', 'require');
    }

    //nickname  更新昵称场景
    public function sceneNickname()
    {
        return $this->only(['id','nickname'])
            ->append('id', 'require')
            ->append('nickname', 'require');
    }

    public function scenePhone()
    {
        return $this->only(['id','tel'])
            ->append('id', 'require')
            ->append('tel', ['require','regex' => '/^1[34578]\d{9}$/']);
    }

    public function sceneSex()
    {
        return $this->only(['id','sex'])
            ->append('id', 'require')
            ->append('sex', 'require');
    }
    public function sceneHobby()
    {
        return $this->only(['id','hobbies'])
            ->append('id', 'require')
            ->append('hobbies', 'require');
    }
}