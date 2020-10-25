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
 * 货物物流-模型
 * 
 * @author 牧羊人
 * @date 2018-10-22
 */
namespace Admin\Model;
use Common\Model\CBaseModel;
class ShipmentsModel extends CBaseModel {
    function __construct() {
        parent::__construct('shipments');
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
            
            //订单编号
            $type = (int)$info['type'];
            if($type==1) {
                //商品
            }else if($type==2) {
                //发票订单
                $invoiceOrderMod = new InvoiceOrderModel();
                $invoiceOrderInfo = $invoiceOrderMod->getInfo($info['order_id']);
                $info['order_num'] = $invoiceOrderInfo['order_no'];
                
            }
            
            //快递公司
            if($info['express_id']) {
                $expressMod = new ExpressesModel();
                $expressInfo = $expressMod->getInfo($info['express_id']);
                $info['express_name'] = $expressInfo['e_name'];
            }
            
            //运费
            if($info['freight_amount']) {
                $info['format_freight_amount'] = \Zeus::formatToYuan($info['freight_amount']);
            }
            
            //寄送城市
            if($info['district_id']) {
                $cityMod = new CityModel();
                $cityName = $cityMod->getCityName($info['district_id'],'>>');
                $info['city_name'] = $cityName;
            }
            
            //寄件类型
            $info['type_name'] = C('SHIPMENTS_TYPE')[$info['type']];
            
        }
        return $info;
    }
    
}