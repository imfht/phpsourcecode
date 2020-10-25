<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户验证
 */
namespace app\system\validate;
use think\Validate;

class User extends Validate{

    protected $rule = [
        'token'             => 'require|max: 25|token',
        'safepassword'      => 'require|length:6',
        'password_confirm'  => 'require|confirm:safepassword',
        'phone'             => 'require|mobile',
        'code'              => 'require|number|length:6',
    ];

    protected $message = [
        'token'                => '不合法的数据来源',
        'safepassword.require' => '密码必须输入',
        'safepassword.length'  => '密码只能输入6位数字',
        'password_confirm'     => '密码输入不一致',
        'phone'                => '手机号错误',
        'code'                 => '验证码错误',
    ];

    protected $scene = [
        'editPassword'    => ['id','safepassword','password_confirm'], //管理修改登录密码
        'safepassword'    => ['safepassword'],
        'setSafePassword' => ['id','safepassword','password_confirm','code'],
        'getphone'        => ['phone'],
        'bindphone'       => ['phone','code'],
    ];
}