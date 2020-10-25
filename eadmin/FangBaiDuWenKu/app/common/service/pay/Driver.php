<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\service\pay;

use app\common\service\BaseInterface;

/**
 * 支付服务驱动
 */
interface Driver extends BaseInterface
{
    
    /**
     * 获取驱动参数
     */
    public function getDriverParam();
    
    /**
     * 获取基本信息
     */
    public function driverInfo();
    
    /**
     * 配置信息
     */
    public function config();
    
    /**
     * 支付通知
     */
    public function notify();
        /**
     * 支付通知
     */
    public function returnfy();
    
    /**
     * 获取订单号
     */
    public function getOrderSn();
    
    /**
     * 支付
     */
    public function pay($order);
}
