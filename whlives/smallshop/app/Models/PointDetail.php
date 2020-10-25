<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:25
 */

namespace App\Models;

/**
 * 积分明细
 * Class Payment
 * @package App\Models
 */
class PointDetail extends BaseModels
{
    //类型
    const TYPE_INCR = 1;//增加
    const TYPE_RECR = 2;//减少

    const EVENT_SYSTEM_RECHARGE = 1;//系统充值
    const EVENT_SYSTEM_DEDUCT = 2;//系统扣除
    const EVENT_RECHARGE = 3;//充值
    const EVENT_WITHDRAW = 4;//提现
    const EVENT_WITHDRAW_REFUND = 5;//提现退款
    const EVENT_ORDER_PAY = 6;//订单支付
    const EVENT_ORDER_REFUND = 7;//订单退款
    const EVENT_ORDER_REWARD = 8;//订单活动额外奖励

    const EVENT_DESC = [
        self::EVENT_SYSTEM_RECHARGE => '系统充值',
        self::EVENT_SYSTEM_DEDUCT => '系统扣除',
        self::EVENT_RECHARGE => '充值',
        self::EVENT_WITHDRAW => '提现',
        self::EVENT_WITHDRAW_REFUND => '提现退款',
        self::EVENT_ORDER_PAY => '订单支付',
        self::EVENT_ORDER_REFUND => '订单退款',
        self::EVENT_ORDER_REWARD => '订单活动额外奖励',
    ];

    protected $table = 'point_detail';
    protected $guarded = ['id'];

}
