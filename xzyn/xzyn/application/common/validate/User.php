<?php
namespace app\common\validate;
// 用户验证器
use think\Validate;

class User extends Validate
{
    protected $rule = [
        'id' => 'require|number',
        'username|用户名' => 'require|min:1|unique:user',
        'password|密码' => 'require|min:6',
        'repassword|确认密码' => 'require|confirm:password',
        'email|邮箱' => 'email|unique:user',
        'moblie|手机号' => 'mobile|unique:user',
        'sex|性别' => 'require|in:0,1',
        'status|状态' => 'require|in:0,1',
    ];

    protected $scene = [
        'add'   => ['username', 'password', 'repassword', 'email', 'moblie', 'sex', 'status'],
        'edit'  => ['email', 'moblie', 'sex', 'status','id'],
        'password' => ['password', 'repassword','id'],
        'status' => ['status','id'],
        'name' => ['name','id'],
        'login' => ['username','password'],
        'register' => ['username','password','repassword','email','moblie'],
    ];
}