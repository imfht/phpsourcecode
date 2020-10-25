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
 * 会员-模型
 * 
 * @author 牧羊人
 * @date 2018-09-08
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class UserModel extends CBaseModel {
    function __construct() {
        parent::__construct('user');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-09-08
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $this->_cacheDelete($id);
        $info = parent::getInfo($id);
        if($info) {
            
            //头像
            if($info['avatar']) {
                $info['avatar_url'] = IMG_URL . $info['avatar'];
            }
            
            //会员等级
            $info['level_name'] = C('USER_LEVEL')[$info['level']];
            
            //用户类型
            $info['type_name'] = C('USER_VIP_TYPE')[$info['type']];
            
            //认证状态
            $info['indentity_stauts_name'] = C('USER_IDENTITY_STATUS')[$info['indentity_stauts']];
            
        }
        return $info;
    }
    
}