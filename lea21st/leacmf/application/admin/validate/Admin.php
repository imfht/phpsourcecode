<?php
/**
 * Created by PhpStorm.
 * User: YC
 * Date: 2017/4/30
 * Time: 13:51
 */

namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{
    protected $rule = [
        'username|用户名' => 'require|unique:admin|max:32',
        'nickname|昵称'  => 'require|max:20',
        //'password|密码'  => 'length:6,16'
    ];

}