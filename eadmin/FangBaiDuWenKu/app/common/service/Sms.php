<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\service;

/**
 * 短信服务
 */
class Sms extends ServiceBase implements BaseInterface
{
    
    /**
     * 服务基本信息
     */
    public function serviceInfo()
    {
        
        return ['service_name' => '短信服务', 'service_class' => 'Sms', 'service_describe' => '系统短信服务，用于整合多个短信平台', 'author' => 'Bigotry', 'version' => '1.0'];
    }
    
    /**
     * 发送短信
     */
    public function sendSms($parameter)
    {
        
        return $this->driver->sendSms($parameter);
    }
}
