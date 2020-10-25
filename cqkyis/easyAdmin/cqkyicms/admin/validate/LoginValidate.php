<?php

/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/7 9:38
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */
namespace app\admin\validate;
use think\Validate;

class LoginValidate extends Validate
{

    protected $rule=[
        'username'  =>  'require|max:25',
        'password' =>  'require',
        'code'=>'require'
    ];
    protected $message  =   [
        'username.require' => '登录名称不能为空',
        'username.max'     => '名称最多不能超过25个字符',
        'password.require'   => '密码不能为空',
        'code.require'  => '验证码不能为空',
       
    ];

}