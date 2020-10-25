<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 发布管理
 */
namespace app\guard\validate;
use think\Validate;

class Send extends Validate{

    protected $rule = [
        'id'                => 'require|number',
        'member_miniapp_id' => 'require|number',
        'car_num'           => 'require',
        'name'              => 'require',
        'idcard'            => 'require',
        'temperature'       => 'require',
        'phone'             => 'require|mobile',

    ];

    protected $message = [
        'id'                => '未找到项目',
        'member_miniapp_id' => '未找打应用',
        'car_num'           => '车牌号必须填写',
        'temperature'       => '体温必须填写',
        'name'              => '姓名必须填写',
        'idcard'            => '身份证必须填写',
        'phone.require'     => '手机号必须填写',
        'phone.mobile'      => '手机号必须错误',
    ];

    protected $scene = [
        'sign'  => ['id','member_miniapp_id','temperature','name','idcard','phone'],
    ];
}    