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
namespace addons\security;

use app\common\controller\Addons;

/**
 * 安全检测插件
 * @author rainfer <rainfer520@qq.com>
 */
class Security extends Addons
{
    public $info = [
        'name'        => 'Security',
        'title'       => '安全检测',
        'intro'       => '网站安全检测',
        'description' => '网站安全检测',
        'status'      => 1,
        'author'      => 'rainfer',
        'version'     => '1.0.0',
        'admin'       => '1',
        'qq'          => '81818832'
    ];
    /**
     * @var array 插件管理方法,格式:['控制器/操作方法',[参数数组]])
     */
    public $admin_actions = [
        'index'  => ['Admin/securityList'],//管理首页
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
