<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 短信记录-控制器
 * 
 * @author 牧羊人
 * @date 2018-07-20
 */
namespace Admin\Controller;
use Admin\Model\SmsLogModel;
use Admin\Service\SmsLogService;
class SmsLogController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new SmsLogModel();
        $this->service = new SmsLogService();
    }
    
}