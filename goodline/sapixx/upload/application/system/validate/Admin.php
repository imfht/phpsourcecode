<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户验证
 */
namespace app\system\validate;
use think\Validate;

class Admin extends Validate{

    protected $rule = [
        'username'         => 'require',
        'login_id'         => 'require|token',
        'login_password'   => 'require',
        'password'         => 'require|confirm',
        'password_confirm' => 'require',
        'about'            => 'require',
        'captcha'          => 'require|captcha',
        'id'               => 'require|number',
    ];

    protected $message = [
        'id'               => '{%id_error}',
        'username'         => '用户名必须填写',
        'password'         => '密码必须填写',
        'password_confirm' => '两次密码输入不一致',
        'about'            => '备注必须填写',
        'captcha'          => '验证码错误',
        'login_id.require' => '用户名必须填写',
        'login_id.token'   => '令牌数据失效,必须刷新页面再试',
    ];

    protected $scene = [
        'login'    => ['login_id','login_password','captcha'],
        'add'      => ['username','password','password_confirm','about'],
        'edit'     => ['id','username','about'],
        'password' => ['password','password_confirm','about'],
    ];
}