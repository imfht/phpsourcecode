<?php
/**
 * Date: 2018\4\1 0001 1:32
 */

namespace validate;
class User extends \Validate
{
    //验证规则
    protected $rule = [
        'username' => 'require|max:25',
        'password' => 'require',
        'sex' => 'require',
        'phone' => 'require|mobile',
    ];
    //验证消息
    protected $message = [
        'username.require' => '用户名不能为空哦',
        'username.max' => '用户名最多不能超过25个字符',
        'password.require' => '密码必须填写',
        'sex.require' => '性别必须设置',
        'phone.require' => '手机必须填写',
        'phone.mobile' => '请检查手机格式是否正确',
    ];
    //验证场景
    protected $scene = [
        //新增场景
        'add' => ['username','password'],
        //编辑时场景
        'edit' =>['username','password','sex','phone']
    ];

}