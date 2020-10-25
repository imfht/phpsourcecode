<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\AuthRule as AuthRuleModel;
use app\common\controller\Common;
use app\common\model\Module as ModuleModel;

/**
 * 后台基控制器
 * @author rainfer <rainfer520@qq.com>
 */
class Base extends Common
{
    /**
     * 初始化
     * @throws
     */
    public function initialize()
    {
        parent::initialize();
        //检测登录
        if (!$this->checkAdminLogin()) {
            $this->redirect(config('yfcmf.adminpath') . '/Login/index');
        }
        $auth = new AuthRuleModel;
        //检测权限
        $id_curr = $auth->getUrlId();
        if (!$auth->checkAuth($id_curr)) {
            $this->error('没有权限', config('yfcmf.adminpath') . '/Index/index');
        }
        //获取有权限的菜单
        if (request()->module() == 'admin' && request()->controller() == 'Index') {
            $menus = $auth->getIndexRules(session('admin_auth.aid'));
            $id_index = $auth->getUrlId('admin/Index/index');
        } else {
            $menus = $auth->getRules(session('admin_auth.aid'), request()->module());
            $id_index = $id_curr;
        }
        //模块
        $module_model = new ModuleModel();
        $modules_nav = $module_model->where([['status', '=', 1], ['nav_url', 'neq', '']])->order('sort')->select();
        $this->assign('modules_nav', $modules_nav);
        $this->assign('menus', $menus);
        $id_curr_arr = $auth->getParents($id_curr);
        $this->assign('id_curr_arr', $id_curr_arr);
        $this->assign('id_index', $id_index);
    }
    /**
     * 空操作
     * @throws
     */
    public function _empty()
    {
        return $this->fetch('public/404');
    }
}
