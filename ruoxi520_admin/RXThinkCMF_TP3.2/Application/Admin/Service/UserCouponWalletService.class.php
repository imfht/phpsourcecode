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
 * 用户优惠券领券交易记录-服务类
 * 
 * @author 牧羊人
 * @date 2019-01-09
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\UserCouponWalletModel;
use Admin\Model\UserModel;
class UserCouponWalletService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new UserCouponWalletModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2019-01-09
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
        
        //交易类型
        $type = (int)$param['type'];
        if($type) {
            $map['type'] = $type;
        }
        
        return parent::getList($map);
    }
    
}