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
 * 商家认证-控制器
 * 
 * @author 牧羊人
 * @date 2018-10-23
 */
namespace Admin\Controller;
use Admin\Model\BusinessAuthModel;
use Admin\Service\BusinessAuthService;
class BusinessAuthController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new BusinessAuthModel();
        $this->service = new BusinessAuthService();
    }
}