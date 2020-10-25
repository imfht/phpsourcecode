<?php
namespace App\Controller\Admin;

use App\BaseController\AdminBaseController as Base;

/**
 * 后台主页相关
 * @package App\Controller\Admin
 */
class Index extends Base
{
    public function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
        $this->addBreadcrumb('仪表盘','/admin/index/index');
    }

    /**
     * 控制面板
     */
    public function index()
    {
        $this->setSeoTitle('控制面板');
        $this->addBreadcrumb('控制面板','/admin/index/index');
        $this->display();
    }

    /**
     * 系统信息
     */
    public function sysinfo()
    {
        $this->setSeoTitle('系统信息');
        $this->addBreadcrumb('系统信息','/admin/index/sysinfo');
        $systemName = php_uname('s');
        $systemInfo = [
            [
                'name'  => '操作系统名称',
                'value' => $systemName,
                'icon'  => strpos(strtolower($systemName), 'linux') !== false ? 'fa fa-linux fa-5x' : 'fa fa-windows fa-5x',
            ],
            [
                'name'  => '系统版本信息',
                'value' => php_uname('v'),
                'icon'  => 'ion ion-gear-a',
            ],
            [
                'name'  => '系统版本名称',
                'value' => php_uname('r'),
                'icon'  => 'ion ion-ios-speedometer',
            ],
            [
                'name'  => 'PHP运行方式',
                'value' => php_sapi_name(),
                'icon'  => 'ion ion-ios-bolt',
            ],
            [
                'name'  => '前进程用户名',
                'value' => Get_Current_User(),
                'icon'  => 'ion ion-android-person',
            ],
            [
                'name'  => 'PHP版本',
                'value' => PHP_VERSION,
                'icon' => 'ion ion-ios-cog'
            ],
            [
                'name'  => 'Zend版本',
                'value' => Zend_Version(),
                'icon' => 'ion ion-ios-pricetags'
            ],
            [
                'name'  => 'PHP安装路径',
                'value' => DEFAULT_INCLUDE_PATH,
                'icon' => 'ion ion-ios-browsers'
            ],
            [
                'name'  => 'Http请求中Host值',
                'value' => $_SERVER["HTTP_HOST"],
                'icon' => 'ion ion-android-globe'
            ],
            [
                'name'  => '服务器IP',
                'value' => GetHostByName($_SERVER['HTTP_HOST']),
                'icon' => 'ion ion-android-locate'
            ],
            [
                'name'  => '客户端IP',
                'value' => $_SERVER['REMOTE_ADDR'],
                'icon' => 'ion ion-android-wifi'
            ],
            [
                'name'  => '服务器解译引擎',
                'value' => 'Swoole',
                'icon' => 'ion ion-ios-color-filter'
            ],
            [
                'name'  => '服务器语言',
                'value' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
                'icon' => 'ion ion-aperture'
            ],
            [
                'name'  => '服务器Web端口',
                'value' => $_SERVER['SWOOLE_CONNECTION_INFO']['server_port'],
                'icon' => 'ion ion-shuffle'
            ],
            [
                'name'  => '最大上传限制',
                'value' => ini_get("file_uploads") ? ini_get("upload_max_filesize") : "Disabled",
                'icon' => 'ion ion-upload'
            ],
            [
                'name'  => '最大执行时间',
                'value' => ini_get("max_execution_time") . "秒",
                'icon' => 'ion ion-ios-alarm'
            ],
        ];

        $this->assign('systemInfo', $systemInfo);
        $this->display();
    }

    /**
     * 皮肤配置异步加载
     */
    public function skinConfig()
    {
        $this->display();
    }
}