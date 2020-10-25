<?php

// +----------------------------------------------------------------------
// | HopePHP
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.wispx.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: WispX <i@wispx.cn>
// +----------------------------------------------------------------------

// [ 日志操作类 ]

namespace hope;

class Log
{
    /**
     * 记录日志信息
     * @var
     */
    protected static $log;

    /**
     * 获取日志信息
     * @return mixed
     */
    public static function getLog()
    {
        return self::$log;
    }

    /**
     * 记录信息
     * @param $msg
     */
    public static function record($msg)
    {
        self::$log[] = $msg;
    }

    /**
     * 清空日志信息
     */
    public static function clear()
    {
        self::$log = [];
    }

    /**
     * 保存日志信息
     * @return bool
     */
    public static function save()
    {
        $path = RUNTIME_PATH . Config::get('log.path') . DS;

        if (!is_dir($path)) {
            File::createFolder($path);
        }

        if (isset($_SERVER['HTTP_HOST'])) {
            $current_uri = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        } else {
            $current_uri = "cmd:" . implode(' ', $_SERVER['argv']);
        }

        $runtime    = round(microtime(true) - HOPE_START_TIME, 10);
        $reqs       = $runtime > 0 ? number_format(1 / $runtime, 2) : '∞';
        $time_str = ' [运行时间：' . number_format($runtime, 6) . 's] [吞吐率：' . $reqs . 'req/s]';
        $memory_use = number_format((memory_get_usage() - HOPE_START_MEM) / 1024, 2);
        $memory_str = ' [内存消耗：' . $memory_use . 'kb]';
        $file_load  = ' [文件加载：' . count(get_included_files()) . ']';
        $message = '[ info ] [Url：' . $current_uri . ']' . $time_str . $memory_str . $file_load;

        self::record($message);

        $files = File::getFolder($path)['file'];
        if (count($files)) {
            foreach ($files as $file) {
                if ((filemtime($path . $file) + 3600 * 24 * (Config::get('log.time'))) <= time()) {
                    @unlink($path . $file);
                }
            }
        }

        $name = date('Y-m-d') . '.log';
        foreach (self::$log as $value) {
            file_put_contents($path . $name, '[' . date('Y-m-d h:i:s') . '] ' . $value . "\r\n", FILE_APPEND);
        }

        return true;
    }
}