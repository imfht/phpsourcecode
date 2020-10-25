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
 * 订单-模型
 * 
 * @author 牧羊人
 * @date 2018-10-16
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class OrderModel extends CBaseModel {
    function __construct() {
        parent::__construct('order');
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
        $info = parent::getInfo($id);
        if($info) {
            
            //订单类型
            $info['order_type_name'] = C('ORDER_TYPE')[$info['order_type']];
            
            //支付类型
            if($info['pay_type']) {
                $info['pay_type_name'] = C('PAY_TYPE')[$info['pay_type']];
            }
            
            //收货城市
            if($info['district_id']) {
                $cityMod = new CityModel();
                $cityName = $cityMod->getCityName($info['district_id'],'>>');
                $info['city_name'] = $cityName;
            }
            
            //快递费
            $info['format_freight_amount'] = \Zeus::formatToYuan($info['freight_amount']);
            
            //商品总额
            $info['format_amount'] = \Zeus::formatToYuan($info['amount']);
            
            //实际支付额
            $info['format_pay_amount'] = \Zeus::formatToYuan($info['pay_amount']);
            
            //订单状态
            $info['status_name'] = C('ORDER_STATUS')[$info['status']];
            
            //支付状态
            $info['pay_status_name'] = C("ORDER_PAY_STATUS")[$info['pay_status']];
            
            //支付时间
            if($info['pay_time']) {
                $info['format_pay_time'] = date('Y-m-d H:i:s',$info['pay_time']);
            }
            
            //物流状态
            $info['shipping_status_name'] = C('SHIPPING_STATUS')[$info['shipping_status']];
            
            //订单来源
            $info['source_name'] = C("ORDER_SOURCE")[$info['source']];
            
            //发货时间
            if($info['shipping_time']) {
                $info['format_shipping_time'] = date('Y-m-d H:i:s',$info['shipping_time']);
            }
            
            //签收时间
            if($info['sign_time']) {
                $info['format_sign_time'] = date('Y-m-d H:i:s',$info['sign_time']);
            }
            
            //发票类型
            if($info['invoice_type']) {
                $info['invoice_type_name'] = C('INVOICE_TYPE')[$info['invoice_type']];
            }
            
        }
        return $info;
    }
    
}