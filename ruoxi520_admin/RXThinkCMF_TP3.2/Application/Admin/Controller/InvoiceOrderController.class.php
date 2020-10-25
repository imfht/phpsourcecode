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
 * 用户发票申请订单-控制器
 * 
 * @author 牧羊人
 * @date 2018-10-22
 */
namespace Admin\Controller;
use Admin\Model\InvoiceOrderModel;
use Admin\Service\InvoiceOrderService;
use Admin\Model\ShipmentsModel;
class InvoiceOrderController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new InvoiceOrderModel();
        $this->service = new InvoiceOrderService();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2019-01-10
     * (non-PHPdoc)
     * @see \Admin\Controller\BaseController::index()
     */
    function index() {
        parent::index([
            'status'=>(int)$_GET['status'],
        ]);
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
     * 发货
     * 
     * @author 牧羊人
     * @date 2018-10-23
     */
    function shipping() {
        if(IS_POST) {
            $message = $this->service->shipping();
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
                'type'=>2,
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
            $shipInfo['order_num'] = $orderInfo['order_no'];
            $shipInfo['receiver_name'] = $orderInfo['receiver_name'];
            $shipInfo['receiver_mobile'] = $orderInfo['receiver_mobile'];
            $shipInfo['leave_message'] = $orderInfo['leave_message'];
            $shipInfo['remark'] = $orderInfo['remark'];
            $shipInfo['freight_amount'] = $orderInfo['format_freight_amount'];
            $this->assign('info',$shipInfo);
        }
        $this->render();
    }
    
}