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
 * 商家-控制器
 * 
 * @author 牧羊人
 * @date 2018-10-19
 */
namespace Admin\Controller;
use Admin\Model\BusinessModel;
use Admin\Service\BusinessService;
class BusinessController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new BusinessModel();
        $this->service = new BusinessService();
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
            'check_status'=>(int)$_GET['check_status'],
        ]);
    }
    
    /**
     * 商家升级审核
     * 
     * @author 牧羊人
     * @date 2019-01-04
     */
    function checkStatus() {
        if(IS_POST) {
            $message = $this->service->checkStatus();
            $this->ajaxReturn($message);
        }
        $this->render();
    }
    
}