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
 * 用户优惠券-控制器
 * 
 * @author 牧羊人
 * @date 2018-11-28
 */
namespace Admin\Controller;
use Admin\Model\UserCouponModel;
use Admin\Service\UserCouponService;
class UserCouponController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new UserCouponModel();
        $this->service = new UserCouponService();
    }
}