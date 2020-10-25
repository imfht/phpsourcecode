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
 * 商家结算申请单-模型
 * 
 * @author 牧羊人
 * @date 2018-10-24
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class BusinessSettlementOrderModel extends CBaseModel {
    function __construct() {
        parent::__construct('business_settlement_order');
    }
    
    /**
     * 获取缓存信息
     * 
     * @author 牧羊人
     * @date 2018-10-24
     * (non-PHPdoc)
     * @see \Common\Model\CBaseModel::getInfo()
     */
    function getInfo($id) {
        $info = parent::getInfo($id);
        if($info) {
            
            //订单编号
            if($info['order_num']) {
                $orderList = M("order")->field("order_num")->where([
                    'id'    =>array('in',$info['order_num']),
                    'mark'  =>1,
                ])->select();
                $order = array_key_value($orderList,'order_num');
                $info['order_no'] = implode(',', $order);
            }
            
            //结算总金额
            if($info['amount']) {
                $info['format_amount'] = \Zeus::formatToYuan($info['amount']);
            }
            
            //订单状态
            if($info['status']) {
                $info['status_name'] = C('BUSINESS_SETTLEMENT_STATUS')[$info['status']];
            }
            
            //发票类型
            if($info['invoice_type']) {
                $info['invoice_type_name'] = C('INVOICE_TYPE')[$info['invoice_type']];
            }
            
        }
        return $info;
    }
    
}