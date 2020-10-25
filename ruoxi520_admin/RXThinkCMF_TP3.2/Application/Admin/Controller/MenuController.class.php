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
 * 菜单管理-控制器
 * 
 * @author 牧羊人
 * @date 2018-07-16
 */
namespace Admin\Controller;
use Admin\Service\MenuService;
use Admin\Model\MenuModel;
class MenuController extends BaseController {
    function __construct() {
        parent::__construct();
        $this->mod = new MenuModel();
        $this->service = new MenuService();
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-07-17
     * (non-PHPdoc)
     * @see \Admin\Controller\BaseController::edit()
     */
    function edit() {
        
        //获取上级菜单
        $menuList = $this->mod->getChilds(0,false);
        if($menuList) {
            $list = array();
            foreach ($menuList as $val) {
                $key = (int)$val['id'];
                $list[$key] = $val;
                $vlist = $val['children'];
                if($vlist) {
                    foreach ($vlist as &$v) {
                        $k = (int)$v['id'];
                        $v['name'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|--" . $v['name'];
                        $list[$k] = $v;
                        $clist = $v['children'];
                        if($clist) {
                            foreach ($clist as &$vt) {
                                $kt = (int)$vt['id'];
                                $vt['name'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|--" . $vt['name'];
                                $list[$kt] = $vt;
                            }
                        }
                    }
                }
            }
        }
        $this->assign('menuList',$list);
        
        $pid = I("get.pid",0);
        parent::edit([
            'parent_id' =>$pid,
            'is_show'   =>1,
        ]);
    }
    
    /**
     * 删除菜单
     * 
     * @author 牧羊人
     * @date 2018-08-16
     * (non-PHPdoc)
     * @see \Admin\Controller\BaseController::drop()
     */
    public function drop() {
        if(IS_POST) {
            $id = I('post.id');
            $funcNum = $this->mod->where(["parent_id"=>$id,'mark'=>1])->count();
            if($funcNum>0) {
                $this->ajaxReturn(message("当前菜单存在子级,无法删除",false));
                return;
            }
            parent::drop();
        }
    }
    
    /**
     * 批量设置菜单节点
     * 
     * @author 牧羊人
     * @date 2018-11-03
     */
    function batchFunc() {
        if(IS_POST) {
            $message = $this->service->batchFunc();
            $this->ajaxReturn($message);
            return;
        }
        $menuId = (int)$_GET['menu_id'];
        $this->assign('menu_id',$menuId);
        $this->render();
    }
    
    
    /**
     * 获取后台框架配置菜单
     * 
     * @author 牧羊人
     * @date 2018-07-09
     */
    public function getMenuList() {
        $auth = $this->adminAuth;
        $message = $this->service->getMenuList($auth);
        $this->ajaxReturn($message);
    }
    
    /**
     * 获取系统图标
     *
     * @author 牧羊人
     * @date 2018-05-23
     */
    function getSysIcon() {
        $this->render("menu.icon.html");
    }
    
}