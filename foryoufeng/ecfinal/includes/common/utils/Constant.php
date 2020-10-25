<?php

/**
 * 定义网站所需要用到的常量
 * Created by PhpStorm.
 * User: root
 * Date: 7/7/16
 * Time: 10:45 AM
 */
$hour=date('H',time());
$minute=date('i',time());
$second=date('s',time());
$year=date('Y',time());
define('DOUBLE_TIME', time());
class Constant
{
    /* 帐号变动类型 */
    const USER_RECHARGE=0;//用户充值
    const USER_RAPLY=1;//用户提现
    const USER_CHANGE=2;//用户资金调节 涉及用户的余额，用户的积分
    const USER_DEPOSIT=3;//用户资金调节 涉及用户的保证金
    const USER_POINTS=99;//用户的积分

    /*活动商品状态*/
    const FG_SHOW=1;//活动显示
    const FG_HIDDEN=0;//活动隐藏

    /* 订单状态 */
    const OS_UNCONFIRMED = 0; // 未确认
    const OS_CONFIRMED = 1; // 已确认
    const OS_CANCELED = 2; // 已取消
    const OS_INVALID = 3; // 无效
    const OS_RETURNED = 4; // 退货
    const OS_SPLITED = 5; // 已分单
    const OS_SPLITING_PART = 6; // 部分分单
    /* 配送状态 */
    const SS_UNSHIPPED = 0; // 未发货
    const SS_SHIPPED = 1; // 已发货
    const SS_RECEIVED = 2; // 已收货
    const SS_PREPARING = 3; // 配货中
    const SS_PART = 4; // 已发货（部分商品）
    const SS_SHIPPED_ING = 5; // 发货中(处理分单)
    /* 支付状态 */
    const PS_UNPAYED = 0; // 未付款
    const PS_PAYING = 1; // 付款中
    const PS_PAYED = 2; // 已付款
    const PS_PARTPAY = 3; // 部分付款
    /* 订单状态 */
    const WPAY=1;//待支付
    const WSURE=2;//待收货
    const WCOMMENT=3;//待评价
    const REBECK=4;//退换货
    const CANCEL=5;//已取消
    const TRYS=6;//试用订单
    const PAY=7;//已付款
    const VOID=8;//已失效
    const SYSTEM=10;//超过15天不收货系统确认收货
    const ACOMMENT=11;//超过15天不收货系统确认收货
    const ABILLS=12;//已上传付款单

}
