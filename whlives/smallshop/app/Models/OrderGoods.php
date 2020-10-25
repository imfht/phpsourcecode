<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:25
 */

namespace App\Models;

/**
 * 订单商品
 * Class Payment
 * @package App\Models
 */
class OrderGoods extends BaseModels
{
    //售后状态
    const REFUND_NO = 0;//没有售后
    const REFUND_APPLY = 1;//待审核
    const REFUND_ONGOING = 2;//售后中
    const REFUND_DONE = 3;//售后完成
    const REFUND_REPLACE_DONE = 4;//换货完成
    const REFUND_CLOSE = 5;//售后关闭

    const REFUND_DESC = [
        self::REFUND_NO => '没有售后',
        self::REFUND_APPLY => '待审核',
        self::REFUND_ONGOING => '售后中',
        self::REFUND_DONE => '售后完成',
        self::REFUND_REPLACE_DONE => '售后完成',
        self::REFUND_CLOSE => '售后关闭'
    ];

    //发货状态
    const DELIVERY_OFF = 0;
    const DELIVERY_ON = 1;

    const DELIVERY_DESC = [
        self::DELIVERY_OFF => '待发货',
        self::DELIVERY_ON => '已发货'
    ];

    protected $table = 'order_goods';
    protected $guarded = ['id'];
}
