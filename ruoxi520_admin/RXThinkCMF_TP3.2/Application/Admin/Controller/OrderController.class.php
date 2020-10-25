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
 * 订单-控制器
 * 
 * @author 牧羊人
 * @date 2018-10-16
 */
namespace Admin\Controller;
use Admin\Model\OrderModel;
use Admin\Service\OrderService;
use Admin\Model\ShipmentsModel;
use Admin\Model\OrderExtendModel;
class OrderController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new OrderModel();
        $this->service = new OrderService();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-10-22
     * (non-PHPdoc)
     * @see \Admin\Controller\BaseController::index()
     */
    function index() {
        parent::index([
            'status'=>(int)$_GET['status'],
        ]);
    }
    
    /**
     * 修改收货地址
     * 
     * @author 牧羊人
     * @date 2018-10-22
     */
    function updateAddress() {
        if(IS_POST) {
            $message = $this->service->updateAddress();
            $this->ajaxReturn($message);
            return ;
        }
        $id = I("get.id",0);
        if($id) {
            $info = $this->mod->getInfo($id);
            $this->assign('info',$info);
        }
        $this->render();
    }
    
    /**
     * 订单发货
     * 
     * @author 牧羊人
     * @date 2018-10-22
     */
    function delivery() {
        if(IS_POST) {
            $message = $this->service->delivery();
            $this->ajaxReturn($message);
            return ;
        }
        $id = I("get.id",0);
        if($id) {
            //获取订单信息
            $orderInfo = $this->mod->getInfo($id);
            
            //获取物流信息
            $shipmentsMod = new ShipmentsModel();
            $shipInfo = $shipmentsMod->getRowByAttr([
                'order_id'=>$id,
                'type'=>1,
            ]);
            if(!$shipInfo) {
                $shipInfo['order_id'] = $orderInfo['id'];
                $shipInfo['province_id'] = $orderInfo['province_id'];
                $shipInfo['city_id'] = $orderInfo['city_id'];
                $shipInfo['district_id'] = $orderInfo['district_id'];
                $shipInfo['street_id'] = $orderInfo['street_id'];
                $shipInfo['address'] = $orderInfo['address'];
                $shipInfo['freight_amount'] = $orderInfo['freight_amount'];
            }
            $shipInfo['order_num'] = $orderInfo['order_num'];
            $shipInfo['receiver_name'] = $orderInfo['receiver_name'];
            $shipInfo['receiver_mobile'] = $orderInfo['receiver_mobile'];
            $shipInfo['leave_message'] = $orderInfo['leave_message'];
            $shipInfo['remark'] = $orderInfo['remark'];
            $this->assign('info',$shipInfo);
        }
        $this->render();
    }
    
    /**
     * 订单确认
     * 
     * @author 牧羊人
     * @date 2018-10-22
     */
    function confirmOrder() {
        if(IS_POST) {
            $message = $this->service->confirmOrder();
            $this->ajaxReturn($message);
            return ;
        }
        $id = I("get.id",0);
        if($id) {
            $info = $this->mod->getInfo($id);
            $this->assign('info',$info);
        }
        $this->render();
    }
    
    /**
     * 发票详情
     * 
     * @author 牧羊人
     * @date 2018-10-22
     */
    function invoice() {
        $id = I("get.id",0);
        if($id) {
            $info = $this->mod->getInfo($id);
            $this->assign('info',$info);
        }
        $this->render();
    }
    
    /**
     * 线下转账凭证审核
     * 
     * @author 牧羊人
     * @date 2018-10-22
     */
    function transfer() {
        if(IS_POST) {
            $message = $this->service->transfer();
            $this->ajaxReturn($message);
            return ;
        }
        $id = I("get.id",0);
        if($id) {
            $orderExtendMod = new OrderExtendModel();
            $result = $orderExtendMod->getRowByAttr([
                'order_id'=>$id,
            ]);
            $info = $orderExtendMod->getInfo($result['id']);
            $info['order_id'] = $id;
            $this->assign('info',$info);
        }
        //凭证审核状态
        $this->assign('statusList',C('ORDER_EXTEND_STATUS'));
        $this->render();
    }
    
    
}