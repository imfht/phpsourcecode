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
 * 用户积分充值订单-控制器
 * 
 * @author 牧羊人
 * @date 2018-10-18
 */
namespace Admin\Controller;
use Admin\Model\UserPointsOrderModel;
use Admin\Service\UserPointsOrderService;
class UserPointsOrderController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new UserPointsOrderModel();
        $this->service = new UserPointsOrderService();
    }
}