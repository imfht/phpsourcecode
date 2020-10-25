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
 * 钱包-模型
 * 
 * @author 牧羊人
 * @date 2019-01-08
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class WalletModel extends CBaseModel {
    function __construct() {
        parent::__construct('wallet');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2019-01-03
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
            
            //账户余额
            $info['format_balance'] = \Zeus::formatToYuan($info['balance']);
            
            //冻结金额
            $info['format_freeze_amount'] = \Zeus::formatToYuan($info['freeze_amount']);
            
            //收入总额
            $info['format_total_amount'] = \Zeus::formatToYuan($info['total_amount']);
            
        }
        return $info;
    }
    
}