<?php
/**
 * 重庆柯一网络有限公司 版权所有
 * 开发团队:柯一网络 柯一CMS项目组
 * 创建时间: 2018/5/7 13:14
 * 联系电话:023-52889123 QQ：563088080
 * 惟一官网：www.cqkyi.com
 */

namespace app\admin\controller;


use app\admin\model\MenuModel;
use service\AuthService;
use think\Controller;

class Base extends Controller
{

    public function initialize(){
        if(empty(session('user'))){
            $this->redirect('@admin');
        }

        $auth = new AuthService();
        if(request()->controller() != 'Index') {
            if (!$auth->check(request()->controller() . '/' . request()->action(), session('uid')) && session('uid') != 1) {
                $this->error("你无权访问该页面！");
            }
        }
        $menuModel = new MenuModel();
        $result = $menuModel->checkUserMenu(session('uid'));

        $this->assign('menu',$result);
        /**
         * 测试一下，获取当前控制器和方法
         */
        $ca =strtolower(request()->controller().'/'.request()->action());
        $mp=$menuModel->findByMenuRole($ca);
        $this->assign('mp',$mp);
        

    }



    protected function ky_success($message, $jumpUrl = '') {
        $str = '<script>';
        $str .='parent.success("' . $message . '",\'jumpUrl("' . $jumpUrl . '")\');';
        $str.='</script>';
        exit($str);
    }

    protected function ky_error($message) {
        $str = '<script>';
        $str .='parent.error("' . $message . '" );';
        $str.='</script>';
        exit($str);
    }



}