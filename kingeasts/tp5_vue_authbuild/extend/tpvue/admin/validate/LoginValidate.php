<?php
// 用户登录验证
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
namespace tpvue\admin\validate;

use think\Validate;

class LoginValidate extends Validate
{
    protected $rule = [
		'loginId'  =>  'require',
		'password' =>  'require|length:6,16',
    ];

    protected $message = [
		'loginId.require'  =>  '请输入账户！',
		'password.require' =>  '请输入6-16位密码！',
		'password.length'  =>  '密码长度不符合!',
    ];
}