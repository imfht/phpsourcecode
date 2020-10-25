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
 * 配置分组-控制器
 * 
 * @author 牧羊人
 * @date 2018-09-22
 */
namespace Admin\Controller;
use Admin\Model\ConfigGroupModel;
use Admin\Service\ConfigGroupService;
class ConfigGroupController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new ConfigGroupModel();
        $this->service = new ConfigGroupService();
    }
}