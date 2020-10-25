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
 * 订单产品-控制器
 * 
 * @author 牧羊人
 * @date 2018-10-22
 */
namespace Admin\Controller;
use Admin\Model\OrderProductModel;
use Admin\Service\OrderProductService;
class OrderProductController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new OrderProductModel();
        $this->service = new OrderProductService();
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
            'order_id'=>(int)$_GET['order_id'],
        ]);
    }
    
}