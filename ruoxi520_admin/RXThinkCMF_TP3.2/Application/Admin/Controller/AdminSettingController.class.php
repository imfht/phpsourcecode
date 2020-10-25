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
 * 系统设置-控制器
 * 
 * @author 牧羊人
 * @date 2018-07-18
 */
namespace Admin\Controller;
use Admin\Model\AdminSettingModel;
use Admin\Service\AdminSettingService;
class AdminSettingController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new AdminSettingModel();
        $this->service = new AdminSettingService();
    }
}