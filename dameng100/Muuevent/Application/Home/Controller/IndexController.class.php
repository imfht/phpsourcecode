<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

use Common\Controller\CommonController;


/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends CommonController
{
    //系统首页
    public function index()
    {
        hook('homeIndex');
        $default_url = C('DEFUALT_HOME_URL');//获得配置，如果为空则显示聚合，否则跳转
        if ($default_url != ''&&strtolower($default_url)!='home/index/index') {
            redirect(get_nav_url($default_url));
        }
        
        $indexType=modC('HOME_INDEX_TYPE','static_home','Home');
        if($indexType=='static_home'){
            $this->display('static_home');
            exit;
        }
        if($indexType=='login'){
            if(!is_login()){
                redirect(U('Ucenter/Member/login'));
                exit;
            }
        }
        
        $show_blocks = get_kanban_config('BLOCK', 'enable', array(), 'Home');
        $this->assign('showBlocks', $show_blocks);
        $enter = modC('ENTER_URL', '', 'Home');
        $this->assign('enter', get_nav_url($enter));
        $this->display();
    }

}