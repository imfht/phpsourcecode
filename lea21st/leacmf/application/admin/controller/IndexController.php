<?php

namespace app\admin\controller;

use think\db\Query;
use think\facade\Config;

class IndexController extends BaseController
{
    public function index()
    {
        $sys_info = cache('sys_cache_server_info');
        if (!$sys_info) {
            $sys_info = $this->getServerInfo();
            cache('sys_cache_server_info', $sys_info, 10 * 60);
        }
        return view('index', [
            'sys_info' => $sys_info
        ]);
    }

    /**
     * 获取系统信息
     * @return mixed
     */
    protected function getServerInfo()
    {
        $sys_info['os']             = PHP_OS;
        $sys_info['zlib']           = function_exists('gzclose') ? 'YES' : 'NO'; //zlib
        $sys_info['safe_mode']      = (boolean)ini_get('safe_mode') ? 'YES' : 'NO'; //safe_mode = Off
        $sys_info['timezone']       = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
        $sys_info['curl']           = function_exists('curl_init') ? 'YES' : 'NO';
        $sys_info['web_server']     = $_SERVER['SERVER_SOFTWARE'];
        $sys_info['phpv']           = phpversion();
        $sys_info['ip']             = GetHostByName($_SERVER['SERVER_NAME']);
        $sys_info['fileupload']     = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknown';
        $sys_info['max_ex_time']    = @ini_get("max_execution_time") . 's'; //脚本最大执行时间
        $sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false;
        $sys_info['domain']         = $_SERVER['HTTP_HOST'];
        $sys_info['memory_limit']   = ini_get('memory_limit');
        $dbPort                     = Config::get('database.prefix');
        $dbHost                     = Config::get('database.prefix');
        $dbHost                     = empty($dbPort) || $dbPort == 3306 ? $dbHost : $dbHost . ':' . $dbPort;

        $musql_version             = (new Query())->query('select version() as ver');
        $sys_info['mysql_version'] = $musql_version[0]['ver'];
        if (function_exists("gd_info")) {
            $gd                 = gd_info();
            $sys_info['gdinfo'] = $gd['GD Version'];
        } else {
            $sys_info['gdinfo'] = "未知";
        }

        return $sys_info;
    }

    public function system()
    {
        $mode   = "/(cpu)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)[\s]+([0-9]+)/";
        $string = shell_exec("more /proc/stat");
        preg_match_all($mode, $string, $arr);
        //print_r($arr);
        $total1 = $arr[2][0] + $arr[3][0] + $arr[4][0] + $arr[5][0] + $arr[6][0] + $arr[7][0] + $arr[8][0] + $arr[9][0];
        $time1  = $arr[2][0] + $arr[3][0] + $arr[4][0] + $arr[6][0] + $arr[7][0] + $arr[8][0] + $arr[9][0];

        sleep(1);
        $string = shell_exec("more /proc/stat");
        preg_match_all($mode, $string, $arr);
        $total2 = $arr[2][0] + $arr[3][0] + $arr[4][0] + $arr[5][0] + $arr[6][0] + $arr[7][0] + $arr[8][0] + $arr[9][0];
        $time2  = $arr[2][0] + $arr[3][0] + $arr[4][0] + $arr[6][0] + $arr[7][0] + $arr[8][0] + $arr[9][0];
        $time   = $time2 - $time1;
        $total  = $total2 - $total1;
        //echo "CPU amount is: ".$num;
        $percent   = bcdiv($time, $total, 5);
        $cpu_usage = round($percent * 100, 3) . '%';

        $str  = shell_exec("more /proc/meminfo");
        $mode = "/(.+):\s*([0-9]+)/";
        preg_match_all($mode, $str, $arr);
        $pr = bcdiv($arr[2][1], $arr[2][0], 5);
        $pr = round($pr * 100, 3) . '%';

        $this->success('success', '', [
            'cpu_usage' => $cpu_usage,
            'mem_usage' => $pr
        ]);
    }

    //更新侧边栏状态
    public function flexible()
    {
        $menu = $this->request->get('menu', 'open', 'trim');
        session('menu_status', $menu);
    }
}
