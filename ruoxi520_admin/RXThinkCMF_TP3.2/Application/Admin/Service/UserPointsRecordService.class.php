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
 * 用户积分记录-服务类
 * 
 * @author 牧羊人
 * @date 2018-10-16
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\UserPointsRecordModel;
use Admin\Model\UserModel;
class UserPointsRecordService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new UserPointsRecordModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-10-16
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
        
        //积分类型
        $points_type = (int)$param['points_type'];
        if($points_type) {
            $map['points_type'] = $points_type;
        }
        
        return parent::getList($map);
    }
    
}