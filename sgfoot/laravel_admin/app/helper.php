<?php
/**
 * 自定义辅助函数
 * User: Administrator
 * Date: 2018/4/24
 * Time: 16:54
 */
define('SGLOGS_PATH', str_replace('\\', '/', dirname(__DIR__)) . '/' . 'public/logs/');
if (!function_exists('mylog')) {
    /**
     * 写日志
     * @param $data
     * @param string $flag
     * @param bool $is
     * @param string $title
     * @return bool
     */
    function mylog($data, $flag = 'None', $is = false, $title = '断点日志')
    {
        return \App\Plugin\SgLogs::write($data, $flag, $is, 'debug', $title);
    }
}