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
 * 用户积分充值订单-模型
 * 
 * @author 牧羊人
 * @date 2018-10-18
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class UserPointsOrderModel extends CBaseModel {
    function __construct() {
        parent::__construct('user_points_order');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-10-18
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id,true);
        if($info) {
            
            //用户信息
            if($info['user_id']) {
                $userMod = new UserModel();
                $userInfo = $userMod->getInfo($info['user_id']);
                $info['mobile'] = $userInfo['mobile'];
            }
            
        }
        return $info;
    }
    
}