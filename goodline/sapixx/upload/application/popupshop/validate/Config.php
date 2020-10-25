<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商城配置
 */
namespace app\popupshop\validate;
use think\Validate;

class Config extends Validate{

    protected $rule = [
        'tax'                => 'require|number|between: 0,100',
        'profit'             => 'require|number|between: 0,100',
        'cycle'              => 'require|number|between: 0,100',
        'lack_cash'          => 'require|integer|between:100,10000',
        'sale_ordernum'      => 'require|number|between: 0,100',
        'old_users'          => 'require|number|between: 0,100',
        'lock_sale_day'      => 'require|number|between: 0,30',
        'num_referee_people' => 'require|number|between: 0,30',
    ];

    protected $message = [
        'tax'                => '提现手续费必须填写（0-100）',
        'profit'             => '客户利润率必须填写（0-50）',
        'cycle'              => '提现周期必须填写（0-100）',
        'lack_cash'          => '提现限额必须填写（100-10000）',
        'sale_ordernum'      => '活动抢购必须填写（0-100)',
        'old_users'          => '是否老用户必须填写（0-100)',
        'lock_sale_day'      => '限制委托日期必须填写（0-30)',
        'num_referee_people' => '推荐绩效人必须填写（0-30)',
    ];

    protected $scene = [
        'setting' => ['tax','profit','cycle','lack_cash','lock_sale_day','num_referee_people']
    ];
}

