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

namespace app\demo\controller;

use app\common\controller\ControllerBase;

/**
 * 演示控制器
 */
class Demo extends ControllerBase
{
    
    /**
     * 各层引用演示
     */
    public function demoExecute() { $this->logicDemo->demoExecute(); }
    
    /**
     * 事务控制
     */
    public function demoTransaction() { $this->logicDemo->demoTransaction(); }
    
    /**
     * 支付服务
     */
    public function demoPay() { $this->logicDemo->demoPay(); }
    
    /**
     * 云存储服务
     */
    public function demoStorage() { $this->logicDemo->demoStorage(); }
    
    /**
     * 短信服务
     */
    public function demoSendSms() { $this->logicDemo->demoSendSms(); }
    
    /**
     * 数据导入
     */
    public function demoDataImport() { $this->logicDemo->demoDataImport(); }
    
    /**
     * 数据导出
     */
    public function demoDataExport() { $this->logicDemo->demoDataExport(); }
    
    /**
     * 二维码 条形码
     */
    public function demoQrcodeBarcode() { $this->logicDemo->demoQrcodeBarcode(); }
    
}
