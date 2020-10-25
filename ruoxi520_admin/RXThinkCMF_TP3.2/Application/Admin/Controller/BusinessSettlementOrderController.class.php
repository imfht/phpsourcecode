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
 * 商家结算申请单-控制器
 * 
 * @author 牧羊人
 * @date 2018-10-24
 */
namespace Admin\Controller;
use Admin\Model\BusinessSettlementOrderModel;
use Admin\Service\BusinessSettlementOrderService;
class BusinessSettlementOrderController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new BusinessSettlementOrderModel();
        $this->service = new BusinessSettlementOrderService();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2019-01-11
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
     * @date 2018-10-24
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
    
}