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
 * 钱包流水-模型
 * 
 * @author 牧羊人
 * @date 2019-01-08
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class WalletTransModel extends CBaseModel {
    function __construct() {
        parent::__construct('wallet_transactions');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2019-01-08
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
            
            //订单类型
            $info['type_name'] = C('WALLET_TRANS_TYPE')[$info['type']];
            
            //变动前金额
            $info['format_before_balance'] = \Zeus::formatToYuan($info['before_balance']);
            
            //变动金额
            $info['format_postal_amount'] = \Zeus::formatToYuan($info['postal_amount']);
            
            //变动后金额
            $info['format_after_balance'] = \Zeus::formatToYuan($info['after_balance']);
            
            //冻结金额
            $info['format_freeze_amount'] = \Zeus::formatToYuan($info['freeze_amount']);
            
            //处理状态
            $info['status_name'] = C('WALLET_TRANS_STATUS')[$info['status']];
            
        }
        return $info;
    }
    
}