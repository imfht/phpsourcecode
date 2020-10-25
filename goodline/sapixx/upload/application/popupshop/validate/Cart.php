<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 提交订单和购物车
 */
namespace app\popupshop\validate;
use think\Validate;

class Cart extends Validate{

    protected $rule = [
        'address' => 'require|integer|>:0',
        'cart'    => 'require|integer|>:0',
        'ids'     => 'require',

    ];
    
    protected $message = [
        'address' => '收货地址必须填写',
        'cart'    => '提交商品不能为空',
        'ids'     => '提交商品不能为空',
    ];

    protected $scene = [
        'add_order' => ['address','ids'],
        'sale_order' => ['address','cart'],
    ];
}