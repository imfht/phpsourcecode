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
 * 商家认证-服务类
 * 
 * @author 牧羊人
 * @date 2018-10-23
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\BusinessAuthModel;
use Admin\Model\UserModel;
class BusinessAuthService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new BusinessAuthModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-10-24
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::getList()
     */
    function getList() {
        $param = I("request.");
        
        $map = [];
        
        //手机号码
        $mobile = trim($param['mobile']);
        if($mobile) {
            $userMod = new UserModel();
            $userInfo = $userMod->getRowByAttr([
                'mobile'=>$mobile,
            ]);
            $map['user_id'] = $userInfo['id'];
        }
        
        //状态
        $status = (int)$param['status'];
        if($status) {
            $map['status'] = $status;
        }
        
        //资质类型
        $identity_type = (int)$param['identity_type'];
        if($identity_type) {
            $map['identity_type'] = $identity_type;
        }
        
        return parent::getList($map);
    }
    
}