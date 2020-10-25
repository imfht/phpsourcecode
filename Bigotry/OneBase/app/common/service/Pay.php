<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

namespace app\common\service;

/**
 * 支付服务
 */
class Pay extends ServiceBase implements BaseInterface
{
    
    // 支付成功异步通知处理URL
    const NOTIFY_URL    = 'http://ob.xxx.cn/demo.php/demo/demoPayNotify';
    
    // 同步支付成功跳转URL
    const CALLBACK_URL  = 'http://ob.xxx.cn/demo.php/demo/demoPayCallback';

    /**
     * 服务基本信息
     */
    public function serviceInfo()
    {
        
        return ['service_name' => '支付服务', 'service_class' => 'Pay', 'service_describe' => '系统支付服务，用于整合多个支付平台', 'author' => 'Bigotry', 'version' => '1.0'];
    }
    
}
