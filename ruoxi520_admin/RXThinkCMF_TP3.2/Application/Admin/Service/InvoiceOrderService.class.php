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
 * 用户发票申请订单-服务类
 * 
 * @author 牧羊人
 * @date 2018-10-22
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\InvoiceOrderModel;
use Admin\Model\ShipmentsModel;
class InvoiceOrderService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new InvoiceOrderModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-10-23
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::getList()
     */
    function getList() {
        $param = I("request.");
        
        $map = [];
        
        //申请单状态
        $status = (int)$param['status'];
        if($status) {
            $map['status'] = $status;
        }
        
        //手机号码
        $mobile = trim($param['mobile']);
        if($mobile) {
            $map['mobile'] = array('like',"%{$mobile}%");
        }
        
        return parent::getList($map);
    }
    
    /**
     * 订单确认
     * 
     * @author 牧羊人
     * @date 2018-10-23
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
     * @date 2018-10-23
     */
    function shipping() {
        $result = I('post.', '', 'trim');
        
        if(!$result['order_id']) {
            return message('订单信息不存在',false);
        }
        
        //开启事务
        $this->mod->startTrans();
        
        //物流信息
        $item = [
            'id'=>$result['id'],
            'type'=>2,
            'order_id'=>$result['order_id'],
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
            'id'=>$result['order_id'],
            'status'=>3,
            'receiver_name'=>$result['receiver_name'],
            'receiver_mobile'=>$result['receiver_mobile'],
            'province_id'=>$result['province_id'],
            'city_id'=>$result['city_id'],
            'district_id'=>$result['district_id'],
            'street_id'=>$result['street_id'],
            'address'=>$result['address'],
            'shipping_status'=>2,
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