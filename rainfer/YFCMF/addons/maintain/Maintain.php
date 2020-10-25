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
namespace addons\maintain;

use app\common\controller\Addons;
use think\facade\Env;

/**
 * 显示日常维护
 */
class Maintain extends Addons
{
    public $info = [
        'name'        => 'Maintain',
        'title'       => '日常维护',
        'intro'       => '后台首页日常维护',
        'description' => '后台首页日常维护',
        'status'      => 1,
        'author'      => 'rainfer',
        'version'     => '1.0.0',
        'admin'       => '0',
        'qq'          => '81818832'
    ];

    /**
     * @var array 插件钩子
     */
    public $hooks = [
        // 钩子名称 => 钩子说明
        'maintain' => '日常维护钩子'
    ];
    /**
     * @var array 插件管理方法,格式:['控制器/操作方法',[参数数组]])
     */
    public $admin_actions = [
        'index'  => [],//管理首页
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

    /**
     * 实现的maintain钩子方法
     * @return mixed
     * @throws \Exception
     */
    public function maintain()
    {
        $config = $this->getConfigValue();
        if (isset($config['display']) && $config['display']) {
            //安全检测
            $system_safe = true;
            //调试模式
            $danger_mode_debug = config('yfcmf.app_debug');
            if ($danger_mode_debug) {
                $system_safe = false;
            }
            $this->assign('danger_mode_debug', $danger_mode_debug);

            //数据库密码
            $weak_setting_db_password = false;
            $weak_pwd_reg             = [
                '/^[0-9]{0,6}$/',
                '/^[a-z]{0,6}$/',
                '/^[A-Z]{0,6}$/'
            ];
            foreach ($weak_pwd_reg as $reg) {
                if (preg_match($reg, config('database.password'))) {
                    $weak_setting_db_password = true;
                    break;
                }
            }
            if ($weak_setting_db_password) {
                $system_safe = false;
            }
            $this->assign('weak_setting_db_password', $weak_setting_db_password);

            //密码修改时间
            $weak_setting_admin_last_change_password = (session('admin_auth.admin_last_change_pwd_time') < time() - 3600 * 24 * 30);
            if ($weak_setting_admin_last_change_password) {
                $system_safe = false;
            }
            $this->assign('weak_setting_admin_last_change_password', $weak_setting_admin_last_change_password);

            //整体安全性
            $this->assign('system_safe', $system_safe);

            //页面调试
            $this->assign('system_pageshow', config('yfcmf.app_trace'));
            //日志分析
            $log_size     = 0;
            $log_file_cnt = 0;
            foreach (list_file(Env::get('runtime_path') . 'log/') as $f) {
                if ($f ['isDir']) {
                    foreach (list_file($f ['pathname'] . '/', '*.log') as $ff) {
                        if ($ff ['isFile']) {
                            $log_size += $ff ['size'];
                            $log_file_cnt++;
                        }
                    }
                }
            }
            $this->assign('log_size', $log_size);
            $this->assign('log_file_cnt', $log_file_cnt);
            echo $this->fetch('maintain');
        }
    }
}
