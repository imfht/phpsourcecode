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
 * 布局-控制器
 * 
 * @author 牧羊人
 * @date 2018-07-17
 */
namespace Admin\Controller;
use Admin\Model\LayoutModel;
use Admin\Service\LayoutService;
class LayoutController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new LayoutModel();
        $this->service = new LayoutService();
    }
}