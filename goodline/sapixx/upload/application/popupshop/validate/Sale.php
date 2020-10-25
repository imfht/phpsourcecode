<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 活动管理
 */
namespace app\popupshop\validate;
use think\Validate;

class Sale extends Validate{

    protected $rule = [
        'house_id'     => 'require|number',
        'cost_price'   => 'require|float',
        'entrust_price'=> 'require|float',
        'sale_price'   => 'require|float',
        'gift'         => 'require|array',
        'order_no'     => 'require',
        'sign'         => 'require',
        'service'      => 'require|integer|=:1',
    ];

    protected $message = [
        'house_id'              => '商品必须选择',
        'cost_price.require'    => '成本价不能为空',
        'cost_price.float'      => '成本价只能填写数字',
        'entrust_price.require' => '委托价不能为空',
        'entrust_price.float'   => '委托价只能填写数字',
        'sale_price.require'    => '销售价不能为空',
        'sale_price.float'      => '销售价只能填写数字',
        'gift'                  => '赠送产品必须填写',
        'order_no'              => '订单号不能为空',
        'sign'                  => '签名不能为空',
        'service'               => '服务协议必须选择',
    ];

    protected $scene = [
        'save'            => ['house_id','cost_price','entrust_price','sale_price','gift'],
        'saleOrderReview' => ['order_no','sign'],
        'giftAction'      => ['order_no','sign','service','gift'],
    ];
}