<?php
// +----------------------------------------------------------------------
// | RXThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2019 http://rxthink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 菜单管理-控制器
 * 
 * @author 牧羊人
 * @date 2018-12-10
 */
namespace app\admin\controller;
use app\admin\model\MenuModel;
use app\admin\service\MenuService;
class MenuController extends AdminBaseController
{
    /**
     * 构造方法
     * 
     * @author 牧羊人
     * @date 2018-12-10
     */
    function __construct()
    {
        parent::__construct();
        $this->model = new MenuModel();
        $this->service = new MenuService();
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2018-12-12(non-PHPdoc)
     * @see \app\admin\controller\AdminBaseController::edit()
     */
    function edit()
    {
        $menuList = $this->model->getChilds(0,false);
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
        
        $pid = input("get.pid",0);
        return parent::edit([
            'parent_id' =>$pid,
            'is_show'   =>1,
        ]);
    }
    
    /**
     * 获取导航菜单
     * 
     * @author 牧羊人
     * @date 2018-12-10
     * (non-PHPdoc)
     * @see \app\admin\controller\AdminBaseController::getNavbar()
     */
    function getNavbar()
    {
        $auth = $this->system_auth;
        $message = $this->service->getNavbar($auth);
        return $message;
    }
    
    /**
     * 批量设置菜单节点
     * 
     * @author 牧羊人
     * @date 2018-12-12
     */
    function batchFunc()
    {
        if(IS_POST) {
            $message = $this->service->batchFunc();
            return $message;
        }
        $this->assign('menu_id',(int)$_GET['menu_id']);
        return $this->render();
    }
    
    /**
     * 获取系统图标
     * 
     * @author 牧羊人
     * @date 2018-12-12
     */
    function getSysIcon()
    {
        return $this->render('icon');
    }
    
}