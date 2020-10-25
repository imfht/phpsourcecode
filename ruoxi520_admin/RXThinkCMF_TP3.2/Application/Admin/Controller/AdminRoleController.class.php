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
 * 角色管理-控制器
 * 
 * @author 牧羊人
 * @date 2018-07-16
 */
namespace Admin\Controller;
use Admin\Service\AdminRoleService;
use Admin\Model\AdminRoleModel;
class AdminRoleController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->service = new AdminRoleService();
        $this->mod = new AdminRoleModel();
    }
    
    /**
     * 删除
     * 
     * @author 牧羊人
     * @date 2018-08-16
     * (non-PHPdoc)
     * @see \Admin\Controller\BaseController::drop()
     */
    function drop() {
        if(IS_POST) {
            $id = I('post.id');
            $info = $this->mod->getInfo($id);
            if($info['auth']) {
                $this->ajaxReturn(message("当前角色已经配置了权限，无法删除",false));
                return;
            }
            parent::drop();
        }
    }
    
}