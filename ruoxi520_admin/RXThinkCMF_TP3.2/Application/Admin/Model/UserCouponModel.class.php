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
 * 用户优惠券-模型
 * 
 * @author 牧羊人
 * @date 2018-11-28
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class UserCouponModel extends CBaseModel {
    function __construct() {
        parent::__construct('user_coupon');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-11-28
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id);
        if($info) {
            
            //领券用户
            if($info['user_id']) {
                $userMod = new UserModel();
                $userInfo = $userMod->getInfo($info['user_id']);
                $info['mobile'] = $userInfo['mobile'];
            }
            
            //优惠券面值
            $info['format_face_value'] = \Zeus::formatToYuan($info['face_value']);
            
            //有效期开始时间
            if($info['start_time']) {
                $info['format_start_time'] = date('Y-m-d H:i:s',$info['start_time']);
            }
            
            //有效期结束时间
            if($info['end_time']) {
                $info['format_end_time'] = date('Y-m-d H:i:s',$info['end_time']);
            }
            
            //满减金额
            if($info['amount']) {
                $info['format_amount'] = \Zeus::formatToYuan($info['amount']);
            }
            
            //使用时间
            if($info['use_time']) {
                $info['format_use_time'] = date('Y-m-d H:i:s',$info['use_time']);
            }
            
            //过期时间
            if($info['expired_time']) {
                $info['format_expired_time'] = date('Y-m-d H:i:s',$info['expired_time']);
            }
            
        }
        return $info;
    }
    
}