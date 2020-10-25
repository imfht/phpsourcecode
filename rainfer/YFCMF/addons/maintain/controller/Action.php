<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: rainfer <81818832@qq.com>
// +----------------------------------------------------------------------
namespace addons\maintain\controller;

use app\admin\controller\Base;
use think\facade\Cache;
use think\facade\Env;

class Action extends Base
{
    /*
     * 日常维护
     */
    public function maintain()
    {
        $action = input('action');
        switch ($action) {
            case 'download_log':
            case 'view_log':
                $logs = [];
                foreach (list_file(Env::get('runtime_path') . 'log/') as $f) {
                    if ($f ['isDir']) {
                        foreach (list_file($f ['pathname'] . '/', '*.log') as $ff) {
                            if ($ff ['isFile']) {
                                $spliter = '==========================';
                                $logs [] = $spliter . '  ' . $f ['filename'] . '/' . $ff ['filename'] . '  ' . $spliter . "\n\n" . file_get_contents($ff ['pathname']);
                            }
                        }
                    }
                }
                if ('download_log' == $action) {
                    force_download_content('log_' . date('Ymd_His') . '.log', join("\n\n\n\n", $logs));
                } else {
                    echo '<pre>' . htmlspecialchars(join("\n\n\n\n", $logs)) . '</pre>';
                }
                break;
            case 'clear_log':
                remove_dir(Env::get('runtime_path') . 'log/');
                $this->success('清除日志成功', url('admin/Index/index'));
                break;
            case 'debug_on':
                $data = ['app_debug' => true];
                $res  = sys_config_setbyarr($data);
                if ($res === false) {
                    $this->error('打开调试失败', url('admin/Index/index'));
                } else {
                    Cache::clear();
                    $this->success('已打开调试', url('admin/Index/index'));
                }
                break;
            case 'debug_off':
                $data = ['app_debug' => false];
                $res  = sys_config_setbyarr($data);
                if ($res === false) {
                    $this->error('关闭调试失败', url('admin/Index/index'));
                } else {
                    Cache::clear();
                    $this->success('已关闭调试', url('admin/Index/index'));
                }
                break;
            case 'trace_on':
                $data = ['app_trace' => true];
                $res  = sys_config_setbyarr($data);
                if ($res === false) {
                    $this->error('打开Trace失败', url('admin/Index/index'));
                } else {
                    Cache::clear();
                    $this->success('已打开Trace', url('admin/Index/index'));
                }
                break;
            case 'trace_off':
                $data = ['app_trace' => false];
                $res  = sys_config_setbyarr($data);
                if ($res === false) {
                    $this->error('关闭Trace失败', url('admin/Index/index'));
                } else {
                    Cache::clear();
                    $this->success('已关闭Trace', url('admin/Index/index'));
                }
                break;
        }
    }
}
