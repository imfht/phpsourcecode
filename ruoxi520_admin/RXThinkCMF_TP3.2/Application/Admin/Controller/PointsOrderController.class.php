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
 * 积分兑换订单-控制器
 * 
 * @author 牧羊人
 * @date 2018-10-25
 */
namespace Admin\Controller;
use Admin\Model\PointsOrderModel;
use Admin\Service\PointsOrderService;
use Admin\Model\ShipmentsModel;
class PointsOrderController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new PointsOrderModel();
        $this->service = new PointsOrderService();
    }
    
    /**
     * 确认订单
     * 
     * @author 牧羊人
     * @date 2018-10-25
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
     * @date 2018-10-25
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
                'type'=>3,
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
            $shipInfo['account_remark'] = $orderInfo['account_remark'];
            $shipInfo['remark'] = $orderInfo['remark'];
            $shipInfo['freight_amount'] = $orderInfo['format_freight_amount'];
            $this->assign('info',$shipInfo);
        }
        $this->render();
    }
    
}