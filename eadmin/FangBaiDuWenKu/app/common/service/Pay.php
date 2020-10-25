<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\service;

/**
 * 支付服务
 */


/**
 * 支付服务使用示例代码
 * 
 * 
 *     $PayModel = model('Pay', 'service');
        
        $PayModel->setDriver('Alipay');
        
        $test_order['order_sn'] =  date('ymdhis', time()) . rand(10000, 99999);
        $test_order['body'] =  '测试';
        $test_order['order_amount'] =  0.01;
        
        echo $PayModel->pay($test_order);
 * 
 */

class Pay extends ServiceBase implements BaseInterface
{
    
    const NOTIFY_URL    ='';
    const CALLBACK_URL  ='';
    
    /**
     * 服务基本信息
     */
    public function serviceInfo()
    {
        
        return ['service_name' => '支付服务', 'service_class' => 'Pay', 'service_describe' => '系统支付服务，用于整合多个支付平台', 'author' => 'Bigotry', 'version' => '1.0'];
    }
    
    /**
     * 支付
     */
    public function pay($order)
    {
        
        return $this->driver->pay($order);
    }
    /**
     * 
     */
    public function notify()
    {
        
        return $this->driver->notify();
    }
    public function returnfy()
    {
        
        return $this->driver->returnfy();
    }
}
