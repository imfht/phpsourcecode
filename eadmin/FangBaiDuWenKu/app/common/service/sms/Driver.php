<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\service\sms;

use app\common\service\BaseInterface;

/**
 * 短信服务驱动
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
     * 发送短信
     */
    public function sendSms($parameter);
}
