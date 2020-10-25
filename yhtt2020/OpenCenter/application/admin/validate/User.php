<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: wdx(wdx@ourstu.com)
 * Date: 2018/9/30
 * Time: 14:00
 * ----------------------------------------------------------------------
 */
namespace app\admin\validate;

use think\Validate;

class User extends Validate
{
    /**
     * 验证规则
     */
    protected $rule = [
        'username' =>  'require|unique:user',
        'password' => 'require|min:6',
        'email' => 'require|email',
        'mobile' => 'require|mobile',
        'group_id' =>  'require|gt:0'
    ];

    /**
     * 提示消息
     */
    protected $message  =   [
        'username.require' => '用户名必填',
        'username.unique' => '用户名已存在',
        'password.require' => '密码必填',
        'password.min' => '密码不少于6位',
        'email.require' => '邮箱必填',
        'email.email' => '邮箱格式不正确',
        'mobile.require' => '手机号必填',
        'mobile.mobile' => '手机号格式不正确',
        'group_id.require' => '角色必选',
        'group_id.gt' => '角色必选'
    ];

    protected $scene = [
        'edit' => ['username', 'email', 'mobile', 'group_id'],
        'add' => ['username', 'password', 'email', 'mobile', 'group_id']
    ];
}