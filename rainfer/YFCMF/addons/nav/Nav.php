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
namespace addons\nav;

use app\common\controller\Addons;

/**
 * 前台导航
 */
class Nav extends Addons
{
    public $info = [
        'name'        => 'Nav',
        'title'       => '前台导航',
        'intro'       => '前台导航菜单管理',
        'description' => '前台导航菜单管理',
        'status'      => 1,
        'author'      => 'rainfer',
        'version'     => '1.0.0',
        'admin'       => '1',//是否有管理页面
        'qq'          => '81818832'
    ];

    /**
     * @var string 原数据库表前缀
     * 用于在导入插件sql时，将原有的表前缀转换成系统的表前缀
     * 一般插件自带sql文件时才需要配置
     */
    public $database_prefix = 'yf_';

    /**
     * @var array 插件钩子
     */
    public $hooks = [
        // 钩子名称 => 钩子说明
    ];

    /**
     * @var array 插件管理方法,格式:['控制器/操作方法',[参数数组]])
     */
    public $admin_actions = [
        'index'  => ['Admin/menuIndex'],//管理首页
        'config' => [],//设置页
        'edit'   => [],//编辑页
        'add'    => [],//增加页
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }
}
