<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/4/24
 * Time: 9:13
 */

namespace app\admin\validate;
use think\Validate;

class Setting extends Validate
{
    protected $rule = [
        'title'  => 'require',
        'url' => 'require|url'
    ];

    protected $message  =   [
        'title.require'      => '网站名称必须',
        'url.require'        => '网站域名必须',
        'url.url'            => '网站地址不正确',
    ];
}