<?php
namespace app\index\controller;

use app\common\controller\Common;

class Index extends Common
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        // 获得系统首页配置，如果为空则继续，否则跳转
        $default_url = config('DEFUALT_HOME_URL');
        if ($default_url != '' && strtolower($default_url)!='index/index/index') {
            $this->redirect(get_nav_url($default_url));
        }
        // 获取首页类型，默认静态首页
        $type = modC('CONFIG_INDEX_TYPE','static_index','index');
        if($type=='static_index'){

            $tpl = modC('CONFIG_STATIC_TPL','static_index','index');

            return $this->fetch($tpl);
        }
        // 登陆页
        if($type=='login'){
            if(is_login() == 0){
                $this->redirect('ucenter/Member/login');
            }
        }
        // 聚合首页
        $show_blocks = get_kanban_config('BLOCK', 'enable', [], 'index');
        $this->assign('showBlocks', $show_blocks);
        $enter = modC('ENTER_URL', '', 'Home');
        $this->assign('enter', get_nav_url($enter));
        return $this->fetch();
    }

    
}
