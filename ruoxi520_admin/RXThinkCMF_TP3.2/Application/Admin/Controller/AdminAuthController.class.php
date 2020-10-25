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
 * 权限设置-控制器
 * 
 * @author 牧羊人
 * @date 2018-07-19
 */
namespace Admin\Controller;
use Admin\Service\AdminAuthService;
class AdminAuthController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->service = new AdminAuthService();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-07019
     * (non-PHPdoc)
     * @see \Admin\Controller\BaseController::index()
     */
    function index() {
        if(IS_POST) {
            $message = $this->service->getList();
            $this->ajaxReturn($message);
            return ;
        }
        
        //参数接收
        $type = I("get.type",0);
        $typeId = I("get.type_id",0);
        $this->assign('type',$type);
        $this->assign("type_id",$typeId);
        $this->render();
    }
    
    /**
     * 保存权限设置
     * 
     * @author 牧羊人
     * @date 2018-07-19
     */
    function setAuth() {
        if(IS_POST) {
            $message = $this->service->setAuth();
            $this->ajaxReturn($message);
            return ;
        }
    }
    
}