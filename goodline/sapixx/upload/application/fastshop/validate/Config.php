<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商城配置
 */
namespace app\fastshop\validate;
use think\Validate;

class Config extends Validate{

    protected $rule = [
        'shop_types'         => 'require|number|between: 0,1',
        'regvip_price'       => 'require|number|between: 0,10000',
        'regvip_level1_ratio'=> 'require|number|between: 0,50',
        'regvip_level2_ratio'=> 'require|number|between: 0,50',
        'reward_types'       => 'require|number|between: 0,1000',
        'reward_nth'         => 'require|number|between: 0,50',
        'reward_ratio'       => 'require|number|between: 0,50',
        'tax'                => 'require|number|between: 0,100',
        'profit'             => 'require|number|between: 0,100',
        'shopping'           => 'require|number|between: 0,50',
        'cycle'              => 'require|number|between: 0,100',
        'amountlimit'        => 'require|number',
        'message'            => 'require|length:0,30',
        'payment_type_shop'  => 'require|integer|between:0,3',
        'payment_point_shop' => 'require|integer|between: 0,100',
        'payment_type'       => 'require|integer|between:0,3',
        'payment_point'      => 'require|integer|between: 0,100',
        'lack_cash'          => 'require|integer|between:100,10000',
        'shopping_name'      => 'require',
        'day_ordernum'       => 'require|number|between: 0,100',
        'sale_ordernum'      => 'require|number|between: 0,100',
        'old_users'          => 'require|number|between: 0,100',
        'platform_ratio'     => 'require|number|between: 0,10',
        'platform_amout'     => 'require|number',
        'lock_sale_day'      => 'require|number|between: 0,30',
        'is_priority'        => 'require|number|between: 0,1',
    ];

    protected $message = [
        'shop_types'         => '购买限制必须选择',
        'regvip_price'       => '开通会员必须填写（0-10000）',
        'regvip_level1_ratio'=> '推荐开会员奖励:一级返比必须填写（0-50）',
        'regvip_level2_ratio'=> '推荐开会员奖励:二级返比必须填写（0-50）',
        'reward_types'       => '佣金规则基准人数必须填写（0-1000）',
        'reward_nth'         => '推荐奖励/奖励倍数必须填写（0-50）',
        'reward_ratio'       => '间接奖励/绩效奖励比例必须填写（0-50）',
        'tax'                => '提现手续费必须填写（0-100）',
        'profit'             => '客户利润率必须填写（0-50）',
        'shopping'           => '购物金比例必须填写（0-50）',
        'amountlimit'        => '提货限额必须填写',
        'cycle'              => '提现周期必须填写（0-100）',
        'message'            => '友情提示15字以内',
        'payment_type_shop'  => '商城支付必须选择是否支持余额支付',
        'payment_point_shop' => '商城支付比例必须在1-100之间',
        'payment_type'       => '抢购支付必须选择是否支持余额支付',
        'payment_point'      => '抢购支付比例必须在1-100之间',
        'lack_cash'          => '提现限额必须填写（100-10000）',
        'shopping_name'      => '购物金名称必须填写',
        'day_ordernum'       => '用户限抢必须填写（0-100)',
        'sale_ordernum'      => '活动抢购必须填写（0-100)',
        'old_users'          => '是否老用户必须填写（0-100)',
        'platform_ratio'     => '平台奖励比例必须填写（0-10)',
        'platform_amout'     => '平台奖励比例必须填写',
        'lock_sale_day'      => '限制委托日期必须填写（0-30)',
        'is_priority'        => '成交顺序必须选择',
    ];

    protected $scene = [
        'save'    => ['shop_types','regvip_price','regvip_level1_ratio','regvip_level2_ratio','reward_nth','reward_types','reward_ratio','tax','profit','shopping','platform_ratio','platform_amout'],
        'setting' => ['payment_type_shop','payment_point_shop','cycle','payment_type','payment_point','lack_cash','amountlimit','day_ordernum','sale_ordernum','old_users','lock_sale_day','is_priority'],        
        'message' => ['message']
    ];
}

