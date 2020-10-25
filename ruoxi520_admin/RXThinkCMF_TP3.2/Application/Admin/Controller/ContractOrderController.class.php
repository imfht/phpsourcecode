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
 * 合同订单-控制器
 * 
 * @author 牧羊人
 * @date 2018-10-23
 */
namespace Admin\Controller;
use Admin\Model\ContractOrderModel;
use Admin\Service\ContractOrderService;
class ContractOrderController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new ContractOrderModel();
        $this->service = new ContractOrderService();
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
     * 合同确认
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
    
}