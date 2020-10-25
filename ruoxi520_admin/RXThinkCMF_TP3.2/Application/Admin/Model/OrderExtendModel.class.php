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
 * 订单扩展-模型
 * 
 * @author 牧羊人
 * @date 2018-10-22
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class OrderExtendModel extends CBaseModel {
    function __construct() {
        parent::__construct('order_extend');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-10-22
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id);
        if($info) {
            
            //支付凭证
            if($info['payment_voucher']) {
                $info['payment_voucher_url'] = IMG_URL . $info['payment_voucher'];
            }
            
        }
        return $info;
    }
    
}