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
 * 订单-服务类
 * 
 * @author 牧羊人
 * @date 2018-10-16
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\OrderModel;
use Admin\Model\ShipmentsModel;
use Admin\Model\OrderExtendModel;
class OrderService extends ServiceModel {
    function __construct() {
        parent::__construct();
        $this->mod = new OrderModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-10-22
     * (non-PHPdoc)
     * @see \Admin\Model\ServiceModel::getList()
     */
    function getList() {
        $param = I("request.");
        
        $map = [];
        
        //状态
        $status = (int)$param['status'];
        if($status) {
            $map['status'] = $status;
        }
        
        //订单编号
        $order_num = trim($param['order_num']);
        if($order_num) {
            $map['order_num'] = array('like',"%{$order_num}%");
        }
        
        //手机号
        $mobile = trim($param['mobile']);
        if($mobile) {
            $map['mobile'] = $mobile;
        }
        
        return parent::getList($map);
    }
    
    /**
     * 修改收货地址
     * 
     * @author 牧羊人
     * @date 2018-10-22
     */
    function updateAddress() {
        return parent::edit();
    }
    
    /**
     * 订单发货
     * 
     * @author 牧羊人
     * @date 2018-10-22
     */
    function delivery() {
        $result = I('post.', '', 'trim');
        
        if(!$result['order_id']) {
            return message('订单信息不存在',false);
        }
        
        //开启事务
        $this->mod->startTrans();
        
        //物流信息
        $item = [
            'id'=>$result['id'],
            'type'=>1,
            'order_id'=>$result['order_id'],
            'province_id'=>$result['province_id'],
            'city_id'=>$result['city_id'],
            'district_id'=>$result['district_id'],
            'street_id'=>$result['street_id'],
            'address'=>$result['address'],
            'freight_amount'=>$result['freight_amount'],
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
            'status'=>4,
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
    
    /**
     * 订单确认
     * 
     * @author 牧羊人
     * @date 2018-10-22
     */
    function confirmOrder() {
        $data = I('post.', '', 'trim');
        if(!$data['id']) {
            return message('订单信息不存在',false);
        }
        return parent::edit();
    }
    
    /**
     * 账户凭证审核
     * 
     * @author 牧羊人
     * @date 2018-10-22
     */
    function transfer() {
        $data = I('post.', '', 'trim');
        if(!$data['id']) {
            return message('转账凭证信息不存在',false);
        }
        //订单ID
        $orderId = (int)$data['order_id'];
        if(!$orderId) {
            return message("订单ID不能为空",false);
        }
        
        //开启事务
        $this->mod->startTrans();
        
        $orderExtendMod = new OrderExtendModel();
        $error = '';
        $result = $orderExtendMod->edit($data,$error);
        if(!$result) {
            //事务回滚
            $this->mod->rollback();
            return message($error,false);
        }
        
        //审核成功之后更新订单状态为已发货
        $rs = $this->mod->edit([
            'id'=>$orderId,
            'status'=>3,
        ]);
        if(!$rs) {
            //事务回滚
            $this->mod->rollback();
            return message("订单状态更新失败",false);
        }
        
        //提交事务
        $this->mod->commit();
        
        return message();
        
    }
    
}