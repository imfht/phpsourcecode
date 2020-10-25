<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 地址表单验证器
 */
namespace app\common\validate;
use think\Validate;

class Address extends Validate{

    protected $rule = [
        'token'    => 'require|max:25|token',
        'name'     => 'require',
        'telphone' => 'require',
        'city'     => 'require',
        'address'  => 'require',
    ];

    protected $message = [
        'token'    => '表单禁止重复提交',
        'name'     => '真实姓名必须填写',
        'telphone' => '手机号必须填写',
        'city'     => '省市地区必须填写',
        'address'  => '详细地址必须填写',
    ];

    protected $scene = [
        'add'  => ['name','telphone','city','address'],
    ];
}