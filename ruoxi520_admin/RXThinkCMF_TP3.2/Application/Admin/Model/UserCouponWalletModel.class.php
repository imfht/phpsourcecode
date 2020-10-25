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
 * 用户领券额度交易记录-模型
 * 
 * @author 牧羊人
 * @date 2019-01-09
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class UserCouponWalletModel extends CBaseModel {
    function __construct() {
        parent::__construct('user_coupon_wallet');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2019-01-09
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id);
        if($info) {
            
            //用户信息
            if($info['user_id']) {
                $userMod = new UserModel();
                $userInfo = $userMod->getInfo($info['user_id']);
                $info['mobile'] = $userInfo['mobile'];
            }
            
            //账户变动前余额
            $info['format_before_balance'] = \Zeus::formatToYuan($info['before_balance']);
            
            //本地变动金额
            $info['format_postal_amount'] = \Zeus::formatToYuan($info['postal_amount']);
            
            //账户变动后余额
            $info['format_after_balance'] = \Zeus::formatToYuan($info['after_balance']);
            
        }
        return $info;
    }
    
}