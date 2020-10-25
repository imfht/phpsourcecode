<?php
declare (strict_types = 1);

namespace app\index\validate;

use think\Validate;

class Admin extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'surname' =>  'require',
        'tel' =>  'require|mobile',
        'username'  =>  'require|unique:admin',
        'password' =>  'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message  =   [
        'surname.require' => '姓名不能为空',
        'tel.require' => '手机号不能为空',
        'tel.mobile' => '手机号格式不对',
        'username.require' => '账号不能为空',
        'username.unique' => '账号已经存在',
        'password.require' => '密码不能为空',
    ];
}
