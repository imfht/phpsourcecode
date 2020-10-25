<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/4/24
 * Time: 9:13
 */

namespace app\admin\validate;
use think\Validate;

class Carousel extends Validate
{
    protected $rule = [
        'title' => 'require',
        'url'=>'url'
    ];

    protected $message  =   [
        'title.require'      => '请填写名称',
        'url.url'=>'网址错误'
    ];
}