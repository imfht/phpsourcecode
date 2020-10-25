<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/4/24
 * Time: 9:13
 */

namespace app\admin\validate;
use think\Validate;

class Column extends Validate
{
    protected $rule = [
        'title'  => 'require',
        'name' => 'require',
        'fid' => 'require',
    ];

    protected $message  =   [
        'username.require'      => '栏目名称必须',
        'name.require'      => '栏目标识必须',
        'fid.require' => '所属栏目必须',
    ];
}