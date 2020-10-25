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
 * 优惠券-模型
 * 
 * @author 牧羊人
 * @date 2018-11-28
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class CouponModel extends CBaseModel {
    function __construct() {
        parent::__construct('coupon');
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
        $info = parent::getInfo($id,true);
        if($info) {
            
            //满减金额
            $info['format_amount'] = \Zeus::formatToYuan($info['amount']);
            
            //优惠券面值
            $info['format_face_value'] = \Zeus::formatToYuan($info['face_value']);
            
        }
        return $info;
    }
    
}