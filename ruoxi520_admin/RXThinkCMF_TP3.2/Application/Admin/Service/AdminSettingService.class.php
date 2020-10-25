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
 * 系统设置-服务类
 * 
 * @author 牧羊人
 * @date 2018-07-18
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\AdminSettingModel;
class AdminSettingService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new AdminSettingModel();
    }
}