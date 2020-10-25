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
 * 发票-模型
 * 
 * @author 牧羊人
 * @date 2018-10-16
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class InvoiceModel extends CBaseModel {
    function __construct() {
        parent::__construct('invoice');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-10-16
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id,true);
        if($info) {
            
            //获取开票用户
            if($info['user_id']) {
                $userMod = new UserModel();
                $userInfo = $userMod->getInfo($info['user_id']);
                $info['mobile'] = $userInfo['mobile'];
            }
            
        }
        return $info;
    }
    
}