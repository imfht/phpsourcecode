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
 * 角色-控制器
 * 
 * @author 牧羊人
 * @date 2018-12-12
 */
namespace app\admin\controller;
use app\admin\model\AdminRoleModel;
use app\admin\service\AdminRoleService;
class AdminRoleController extends AdminBaseController
{

    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-12
     */
    function __construct()
    {
        parent::__construct();
        $this->model = new AdminRoleModel();
        $this->service = new AdminRoleService();
    }
    
    /**
     * 删除数据
     * 
     * @author 牧羊人
     * @date 2018-12-12
     * (non-PHPdoc)
     * @see \app\admin\controller\AdminBaseController::drop()
     */
    function drop()
    {
        if(IS_POST) {
            $id = input('post.id');
            $info = $this->model->getInfo($id);
            if($info['auth']) {
                return message("当前角色已经配置了权限，无法删除",false);
            }
            return parent::drop();
        }
    }
    
}