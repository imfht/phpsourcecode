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
 * 职位管理-控制器
 * 
 * @author 牧羊人
 * @date 2018-07-16
 */
namespace Admin\Controller;
use Admin\Model\AdminPositionModel;
use Admin\Service\AdminPositionService;
class AdminPositionController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new AdminPositionModel();
        $this->service = new AdminPositionService();
    }
    
    /**
     * 删除
     *
     * @author 牧羊人
     * @date 2018-08-29
     * (non-PHPdoc)
     * @see \Admin\Controller\BaseController::drop()
     */
    function drop() {
        if(IS_POST) {
            $id = I('post.id');
            $count = M("admin")->where(['position_id'=>$id])->count();
            if($count) {
                $this->ajaxReturn(message("当前职位已经在使用中，无法删除",false));
                return;
            }
            parent::drop();
        }
    }
    
}