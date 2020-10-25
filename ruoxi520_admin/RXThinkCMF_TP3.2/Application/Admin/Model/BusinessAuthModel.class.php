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
 * 商家认证-模型
 * 
 * @author 牧羊人
 * @date 2018-10-23
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class BusinessAuthModel extends CBaseModel {
    function __construct() {
        parent::__construct('user_business_auth');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-10-23
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
            
            //认证类型
            if($info['identity_type']) {
                $info['identity_type_name'] = C('BUSINESS_IDENTITY_AUTH')[$info['identity_type']];
            }
            
            //审核状态
            $info['status_name'] = C('BUSINESS_AUTH_STATUS')[$info['status']];
            
        }
        return $info;
    }
    
}