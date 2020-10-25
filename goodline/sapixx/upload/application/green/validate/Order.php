<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 订单操作
 */
namespace app\green\validate;
use think\Validate;

class Order extends Validate{

    protected $rule = [
        'order_no'          => 'require',
        'express_company'   => 'require',
        'item_id'           => 'require|integer',
        'phone_id'          => 'require|mobile',
        'member_miniapp_id' => 'require|integer',
        'weight'            => 'require|integer',
        'address'           => 'require',
        'longitude'         => 'require|float',
        'latitude'          => 'require|float',
    ];
    
    protected $message = [
        'order_no'          => '单号必须填写',
        'express_company'   => '快递公司或取货方式必须填写',
        'item_id'           => '成交商品必须输入',
        'phone_id'          => '手机号必须正确输入',
        'member_miniapp_id' => '应用ID错误',
        'weight'            => '重量必须选择',
        'address'           => '地址必须填写',
        'longitude'         => '经纬度必须选择',
        'latitude'          => '经纬度必须选择',
    ];

    protected $scene = [
        'sendgoods' => ['order_no','express_company'],
        'sendgift'  => ['item_id','phone_id','member_miniapp_id'],
        //添加预约信息
        'add'  => ['weight','address','longitude','latitude'],
    ];
}