<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo\Log;


use Timo\Config\Config;
use Timo\Core\App;
use Timo\Core\Request;

class Log
{
    /**
     * 记录日志
     *
     * @param $message
     * @param string $logFileName
     * @param bool $single_record 是否将日志记录到单个文件
     * @return bool
     */
    public static function record($message, $logFileName = '', $single_record = false)
    {
        //当日志写入功能关闭时
        if (Config::runtime('log.record') === false) {
            return true;
        }

        $logFilePath = self::getLogFilePath($logFileName, $single_record);
        static::makeLogFolder($logFilePath);

        //日志内容
        $message = static::buildLogContent($message);

        return error_log($message, 3, $logFilePath);
    }

    /**
     * 记录单文件日志
     *
     * @param $message
     * @param string $logFileName
     */
    public static function single($message, $logFileName = '')
    {
        self::record($message, $logFileName, true);
    }

    /**
     * 记录错误日志
     *
     * @param $message
     * @param string $logFileName
     * @param bool $single_record
     */
    public static function error($message, $logFileName = '', $single_record = false)
    {
        self::record($message, 'error' . DIRECTORY_SEPARATOR . $logFileName, $single_record);
    }

    /**
     * 记录调试日志
     *
     * @param $message
     * @param string $logFileName
     * @param bool $single_record
     */
    public static function debug($message, $logFileName = '', $single_record = false)
    {
        self::record($message, 'debug' . DIRECTORY_SEPARATOR . $logFileName, $single_record);
    }

    /**
     * 记录信息
     *
     * @param $message
     * @param string $logFileName
     * @param bool $single_record
     */
    public static function info($message, $logFileName = '', $single_record = false)
    {
        self::record($message, 'info' . DIRECTORY_SEPARATOR . $logFileName, $single_record);
    }

    /**
     * 创建日志内容
     *
     * @param $message
     * @return string
     */
    private static function buildLogContent($message)
    {
        $router = App::controller() . '/' . App::action();
        $client_ip = Request::getClientIP();
        if (is_array($message)) {
            $message = json_encode($message, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }

        return sprintf('[%s %s %s] %s', date('Y-m-d H:i:s'), $client_ip, $router, $message) . "\n";
    }

    /**
     * 创建日志目录
     *
     * @param $logFilePath
     */
    private static function makeLogFolder($logFilePath)
    {
        $logDir = dirname($logFilePath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    /**
     * 获取当前日志文件名
     *
     * @access private
     * @param null $log_file_name 日志文件名 'sql' 'send/wx'
     * @param bool $single_record 是否记录到单文件
     * @return string
     */
    private static function getLogFilePath($log_file_name, $single_record = false)
    {
        $log_file_name = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $log_file_name);
        //组装日志文件路径
        $path = Config::runtime('log.path');
        if (IS_CLI) {
            $path .= 'cli' . DIRECTORY_SEPARATOR;
        }

        if (empty($log_file_name)) {
            $path .= date('Y-m') . DIRECTORY_SEPARATOR . date('d');
        } elseif (!$single_record) {
            $path .= $log_file_name . DIRECTORY_SEPARATOR . date('Y-m') . DIRECTORY_SEPARATOR . date('d');
        } else {
            $path .= $log_file_name;
        }
        $path .= '.log';
        return $path;
    }
}
