<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2016 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------
/**
 *	PC 端门户前台控制器
 *  @version 1.0
 */
namespace app\home\controller;

use think\Controller;
use app\common\model\Config;
use app\common\model\Channel;
use think\Lang;

class Index extends Controller
{
    //系统首页
    public function index()
    {

        $show_blocks = get_kanban_config('BLOCK', 'enable', array(), 'Home');

        $this->assign('showBlocks', $show_blocks);


        $enter = modC('ENTER_URL', '', 'Home');
        $this->assign('enter', get_nav_url($enter));

        $channel = new Channel;
        $navtree = $channel ->lists(true,true); //获取导航栏树
        $sub_menu['left']= array(array('tab' => 'home', 'title' => lang('_SQUARE_'), 'href' =>  url('index'))//,array('tab'=>'rank','title'=>'排行','href'=>url('rank'))
        );

        $this->assign('navtree', $navtree);
        $this->assign('sub_menu', $sub_menu);
        $this->assign('current', 'home');

        $indexType=modC('HOME_INDEX_TYPE','static_home','Home');
        if($indexType=='static_home'){
            return $this->fetch('static_home');
        }
        if($indexType=='login'){
            if(!is_login()){
                redirect(url('Ucenter/Member/login'));
            }
        }
        hook('homeIndex');
        $default_url = config('DEFUALT_HOME_URL');//获得配置，如果为空则显示聚合，否则跳转
        if ($default_url != ''&&strtolower($default_url)!='home/index/index') {
            redirect(get_nav_url($default_url));
        }



        return $this->fetch('index');
    }

    protected function _initialize()
    {
        //自动加载语言文件
        $langSet = Lang::detect();
        Lang::load(APP_PATH . 'home'.DS.'lang'.DS.$langSet.'.php');
        Lang::load(APP_PATH . 'weibo'.DS.'lang'.DS.$langSet.'.php');
        /*读取站点配置*/
        $config = model('Config')->lists();
        config($config); //添加配置

        if (!config('WEB_SITE_CLOSE')) {
            $this->error(lang('_ERROR_WEBSITE_CLOSED_'));
        }
    }

    public function search()
    {
        $keywords=input('post.keywords','','text');
        $modules = model('Common/Module')->getAll();
        foreach ($modules as $m) {
            if ($m['is_setup'] == 1 && $m['entry'] != '') {
                if (file_exists(APP_PATH . $m['name'] . '/Widget/Search.php')) {
                    $mod[] = $m['name'];
                }
            }
        }
        $show_search = get_kanban_config('SEARCH', 'enable', $mod, 'Home');

        $this->assign($keywords);
        $this->assign('showBlocks', $show_search);
        return $this->fetch();
    }

    public function test()
    {
        $path = "application\\index\\controller\\index.php";

        // 定义输出文字
        $html = "<p>我是 [path] 文件的index方法</p>";
        echo $html;
        // 调用temphook钩子, 实现钩子业务
        hook('temphook', ['data'=>$html]);

        // 替换path标签
        return str_replace('[path]', $path, $html);
    }
}
