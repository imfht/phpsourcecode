<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\logic;

/**
 * 测试逻辑
 */
class Test extends AdminBase
{
    
    /**
     * 测试逻辑默认方法
     */
    public function index()
    {
        
        $menu = model('menu');
        
        return $menu->getList();
    }
    
    /**
     * 测试支付
     */
    public function pay()
    {
        
        $PayModel = model('Pay', 'service');
        
        $PayModel->setDriver('Alipay');
        
        $test_order['order_sn'] =  date('ymdhis', time()) . rand(10000, 99999);
        $test_order['body'] =  '测试';
        $test_order['order_amount'] =  0.01;
        
        exit($PayModel->pay($test_order));
    }
    
    /**
     * 测试云存储
     */
    public function storage()
    {
        
        $StorageModel = model('Storage', 'service');
        
        $StorageModel->setDriver('Qiniu');
        
        $StorageModel->upload(130);
    }
}
