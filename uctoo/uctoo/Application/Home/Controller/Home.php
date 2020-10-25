<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2017 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------
/**
 *	PC 端门户管理后台控制器
 *  @version 1.0
 */
namespace app\home\controller;

use  app\admin\builder\AdminConfigBuilder;
use  app\admin\controller\Admin;

class Home extends Admin
{
    public function config()
    {
        $builder = new AdminConfigBuilder();
        $data = $builder->handleConfig();
        if (array_key_exists("OPEN_LOGIN_PANEL", $data)) {
            $data['OPEN_LOGIN_PANEL'] = $data['OPEN_LOGIN_PANEL'];
        }else{
            $data['OPEN_LOGIN_PANEL'] = 1;
        }
        if (array_key_exists("HOME_INDEX_TYPE", $data)) {
            $data['HOME_INDEX_TYPE'] = $data['HOME_INDEX_TYPE'];
        }else{
            $data['HOME_INDEX_TYPE'] = 'static_home';
        }

        $builder->title(lang('_HOME_SETTING_'));
        $builder->keyRadio('HOME_INDEX_TYPE','系统首页类型','',array('static_home'=>'静态首页','index'=>'聚合首页','login'=>'登录页'));
        $modules = model('Module')->getAll();

        foreach ($modules as $m) {
            if ($m['is_setup'] == 1 && $m['entry'] != '') {
                if (file_exists(APP_PATH . $m['name'] . '/widget/HomeBlockWidget.php')) {
                    $module[] = array('data-id' => $m['name'], 'title' => $m['alias']);
                }
            }
        }
        $module[] = array('data-id' => 'slider', 'title' => lang('_CAROUSEL_'));

        $default = array(array('data-id' => 'disable', 'title' => lang('_DISABLED_'), 'items' => $module), array('data-id' => 'enable', 'title' =>lang('_ENABLED_'), 'items' => array()));
        $builder->keyKanban('BLOCK', '展示模块','拖拽到右侧以展示这些模块，新的模块安装后会多出一些可操作的项目');
        if (array_key_exists("BLOCK", $data)) {
            $data['BLOCK'] = $builder->parseNestableArray($data['BLOCK'], $module, $default);
        }else{
            $data['BLOCK'] = $builder->parseNestableArray(null, $module, $default);
        }

        foreach ($modules as $m) {
            if ($m['is_setup'] == 1 && $m['entry'] != '') {
                if (file_exists(APP_PATH . $m['name'] . '/widget/SearchWidget.php')) {
                    $mod[] = array('data-id' => $m['name'], 'title' => $m['alias']);
                }
            }
        }

        $defaultSearch = array(array('data-id' => 'disable', 'title' => lang('_DISABLED_'), 'items' => array()), array('data-id' => 'enable', 'title' =>lang('_ENABLED_'), 'items' => $mod));
        $builder->keyKanban('SEARCH', '全站搜索模块', '拖拽到右侧以展示这些模块，新的模块安装后会多出一些可操作的项目');
        if (array_key_exists("SEARCH", $data)) {
            $data['SEARCH'] = $data['SEARCH'];
        }else{
            $data['SEARCH'] = null;
        }
        $data['SEARCH'] = $builder->parseNestableArray($data['SEARCH'], $mod, $defaultSearch);

        $builder->group('首页类型', 'HOME_INDEX_TYPE');
        $builder->group('聚合首页展示模块', 'BLOCK');
        $builder->group('可供全站搜索模块', 'SEARCH');

        $show_blocks = get_kanban_config('BLOCK_SORT', 'enable', array(), 'Home');
        $show_search = get_kanban_config('SEARCH', 'enable', array(), 'Home');

        $builder->buttonSubmit();

        $builder->data($data);

        return $builder->fetch();
    }
}
