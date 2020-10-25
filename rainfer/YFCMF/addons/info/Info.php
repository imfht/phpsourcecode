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
namespace addons\info;

use app\common\controller\Addons;
use think\facade\App;

/**
 * 后台首页信息显示
 */
class Info extends Addons
{
    public $info = [
        'name'        => 'Info',
        'title'       => '后台信息',
        'intro'       => '后台信息',
        'description' => '后台首页信息显示',
        'status'      => 1,
        'author'      => 'rainfer',
        'version'     => '1.0.0',
        'admin'       => '0',//是否有管理页面
        'qq'          => '81818832'
    ];

    /**
     * @var string 原数据库表前缀
     * 用于在导入插件sql时，将原有的表前缀转换成系统的表前缀
     * 一般插件自带sql文件时才需要配置
     */
    public $database_prefix = '';

    /**
     * @var array 插件钩子
     */
    public $hooks = [
        // 钩子名称 => 钩子说明
        'gitinfo' => 'git信息钩子',
        'sysinfo' => '框架信息钩子'
    ];

    /**
     * @var array 插件管理方法,格式:['控制器/操作方法',[参数数组]])
     */
    public $admin_actions = [
        'index'  => [],//管理首页
        'config' => ['Admin/config'],//设置页
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

    /**
     * 实现的gitinfo钩子方法
     * @return mixed
     * @throws
     */
    public function gitinfo()
    {
        $config = $this->getConfigValue();
        if (isset($config['display']) && $config['display']) {
            echo $this->fetch('gitinfo');
        }
    }

    /**
     * 实现的sysinfo钩子方法
     * @return mixed
     * @throws
     */
    public function sysinfo()
    {
        $config = $this->getConfigValue();
        if (isset($config['display']) && $config['display']) {
            //系统信息
            $info = [
                'os'               => PHP_OS,
                'software'         => $_SERVER["SERVER_SOFTWARE"],
                'upload'           => ini_get('upload_max_filesize'),
                'thinkphp_version' => App::version(),
                'mysql_version'    =>db()->query('select version() as version')[0]['version'],
                'php_version'      =>PHP_VERSION,
                'yfcmf_version'    =>config('yfcmf.yfcmf_version')
            ];
            $this->assign('info', $info);
            echo $this->fetch('sysinfo');
        }
    }
}
