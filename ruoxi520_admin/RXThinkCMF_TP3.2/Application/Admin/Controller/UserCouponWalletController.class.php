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
 * 用户优惠券额度交易记录-控制器
 * 
 * @author 牧羊人
 * @date 2019-01-09
 */
namespace Admin\Controller;
use Admin\Model\UserCouponWalletModel;
use Admin\Service\UserCouponWalletService;
class UserCouponWalletController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new UserCouponWalletModel();
        $this->service = new UserCouponWalletService();
    }
}