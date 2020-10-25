<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------

namespace app\common\validate;

use think\Validate;

/**
 * 验证器
 * @Author: rainfer <rainfer520@qq.com>
 */
class Common extends Validate
{
    protected $rule = [
        'username' => 'require|alphaNum|max:20|unique:admin,username',
        'email'    => 'email',
        'password' => 'require|length:6,16',
        'group_id' => 'gt:0',
        'mobile'   => 'mobile'
    ];

    protected $message = [
        'username.require'  => '用户名必须',
        'username.alphaNum' => '用户名必须为字母或数字',
        'username.max'      => '用户名最多不能超过20个字符',
        'username.unique'   => '用户名重复',
        'email'             => '邮箱格式错误',
        'password.require'  => '密码必须',
        'password.length'   => '密码需在6-16位之间',
        'group_id'          => '必须选择用户组',
        'mobile'            => '手机格式不正确',
    ];

    /**
     * user场景
     * @return mixed
     */
    public function sceneUser()
    {
        return $this->only(['username'])
            ->append('username', 'unique:user,username');
    }

    /**
     * admin场景
     * @return mixed
     */
    public function sceneAdmin()
    {
        return $this->only(['username'])
            ->append('username', 'unique:admin,username');
    }
}
