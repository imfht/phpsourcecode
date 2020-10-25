<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户验证
 */
namespace app\common\validate;
use think\Validate;

class Member extends Validate{

    protected $rule = [
        'id'                   => 'require|number',
        'miniapp_id'           => 'require|number',
        'username'             => 'require',
        'password'             => 'require|confirm',
        'password_confirm'     => 'require',
        'phone_id'             => 'require|mobile',
        'login_id'             => 'require|mobile|token',
        'login_password'       => 'require|min:6',
        'safe_password'        => 'require|integer|length:6',
        'edit_login_password'  => 'min:6',
        'edit_safe_password'   => 'integer|length:6',
        'sms_code'             => 'require',
        'safepassword'         => 'require|confirm:safepassword_confirm',
        'safepassword_confirm' => 'require|integer|length:6',
        'captcha'              => 'require|captcha',
    ];

    protected $message = [
        'id'                           => '用户未找到',
        'miniapp_id'                   => '您的应用没有选择',
        'username'                     => '用户名必须填写',
        'password'                     => '密码必须填写,输入密码不一致',
        'password_confirm'             => '两次密码输入不一致',
        'phone_id'                     => '请输入正确的手机号',
        'login_id.require'             => '登录ID必须填写',
        'login_id.mobile'              => '登录ID必须是手机号',
        'login_id.token'               => '令牌数据失效,必须刷新页面再试',
        'login_password.require'       => '密码必须填写',
        'login_password.min'           => '密码不能小于6位',
        'safe_password.require'        => '请输入安全密码',
        'safe_password.integer'        => '安全密码只能输入6位数字',
        'safe_password.length'         => '安全密码只能输入6位数字',
        'edit_login_password.min'      => '密码不能小于6位',
        'edit_safe_password.integer'   => '安全密码只能输入6位数字',
        'edit_safe_password.length'    => '安全密码只能输入6位数字',
        'sms_code'                     => '短信验证码必须填写',
        'safepassword'                 => '密码必须填写',
        'safepassword.confirm'         => '两次密码输入不一致',
        'safepassword_confirm.integer' => '安全密码只能输入6位数字',
        'safepassword_confirm.length'  => '安全密码只能输入6位数字',
        'captcha'                      => '验证码错误,点击更换新的验证码',
    ];

    protected $scene = [
        'add'          => ['username','phone_id','login_password','safe_password'],
        'edit'         => ['id','username','phone_id','edit_login_password','edit_safe_password'],
        'login'        => ['login_id','login_password','captcha'],
        'reg'          => ['phone_id','login_password','sms_code','username','captcha'],
        'getpasspord'  => ['phone_id','login_password','sms_code','captcha'],
        'password'     => ['id','login_password','password','password_confirm'],  //修改登录密码
        'safepassword' => ['id','login_password','safepassword','safepassword_confirm'],  //修改安全密码
        'updatephone'  => ['id','phone_id','login_password','sms_code'],  //修改手机号
        'bindapp'      => ['miniapp_id','username','phone_id','login_password'],
    ];
}