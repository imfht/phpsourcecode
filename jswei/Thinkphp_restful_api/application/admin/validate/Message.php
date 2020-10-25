<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/4/24
 * Time: 9:13
 */

namespace app\admin\validate;
use think\Validate;

class Message extends Validate
{
    protected $rule = [
        'title'  => 'require',
        'content' => 'require'
    ];

    protected $message  =   [
        'username.require'      => '栏目名称必须',
        'content.require'      => '栏目标识必须'
    ];
}