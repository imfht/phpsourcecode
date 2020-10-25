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
 * 积分兑换订单-服务类
 * 
 * @author 牧羊人
 * @date 2018-10-25
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\PointsOrderModel;
use Admin\Model\ShipmentsModel;
class PointsOrderService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new PointsOrderModel();
    }
    
    /**
     * 确认订单
     * 
     * @author 牧羊人
     * @date 2018-10-25
     */
    function confirmOrder() {
        $data = I('post.', '', 'trim');
        if(!$data['id']) {
            return message('订单信息不存在',false);
        }
        return parent::edit();
    }
    
    /**
     * 发货
     * 
     * @author 牧羊人
     * @date 2018-10-25
     */
    function shipping() {
        $result = I('post.', '', 'trim');
        //订单ID
        $orderId = (int)$result['order_id'];
        if(!$orderId) {
            return message('订单ID不存在',false);
        }
        $info = $this->mod->getInfo($orderId);
        if(!$info) {
            return message('订单信息不存在',false);
        }
        if($info['status']>=2) {
            return message('订单已发货',false);
        }
        
        //开启事务
        $this->mod->startTrans();
        
        //物流信息
        $item = [
            'id'=>$result['id'],
            'type'=>3,
            'order_id'=>$orderId,
            'province_id'=>$result['province_id'],
            'city_id'=>$result['city_id'],
            'district_id'=>$result['district_id'],
            'street_id'=>$result['street_id'],
            'address'=>$result['address'],
            'freight_amount'=>$result['freight_amount']*100,
            'express_no'=>$result['express_no'],
            'express_id'=>$result['express_id'],
            'note'=>$result['note'],
        ];
        $shipmentsMod = new ShipmentsModel();
        $res = $shipmentsMod->edit($item);
        if(!$res) {
            //事务回滚
            $this->mod->rollback();
            return message('物流信息更新失败',false);
        }
        
        //订单信息
        $data = [
            'id'=>$orderId,
            'receiver_name'=>$result['receiver_name'],
            'receiver_mobile'=>$result['receiver_mobile'],
            'province_id'=>$result['province_id'],
            'city_id'=>$result['city_id'],
            'district_id'=>$result['district_id'],
            'address'=>$result['address'],
            'status'=>2,
            'shipping_time'=>time(),
            'note'=>$result['note'],
        ];
        $rs = $this->mod->edit($data);
        if(!$rs) {
            //事务回滚
            $this->mod->rollback();
            return message('订单信息更新失败',false);
        }
        
        //提交事务
        $this->mod->commit();
        
        return message();
    }
    
}