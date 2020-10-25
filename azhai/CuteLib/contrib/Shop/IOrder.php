<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Contrib\Shop;


/**
 * 订单
 */
interface IOrder
{
    const STATUS_CREATED = 1;   // 创建成功
    const STATUS_PENDING = 2;   // (等待)付款中
    const STATUS_PAID = 3;      // 支付成功
    const STATUS_STOCKOUT = 4;  // 缺货/备货中
    const STATUS_SENT = 5;      // 发货/到账成功
    const STATUS_FINISH = 6;    // 已经完成
    const STATUS_CLOSED = 7;    // 订单挂起/取消
    const STATUS_DELAY = 8;     // 订单延期
    const STATUS_EXPIRED = 9;   // 订单过期

    /**
     * 获得订单号
     * @return string 20位以内的订单号
     */
    public function getNumber();

    /**
     * 获得订单金额
     * @return object 订单金额，Amount对象
     */
    public function getAmount();

    /**
     * 获得用户
     * @return object
     */
    public function getUser();

    /**
     * 是否已支付成功
     * @return bool
     */
    public function isPaid();

    /**
     * 货物/款项是否已送达
     * @return bool
     */
    public function isSent();
}
