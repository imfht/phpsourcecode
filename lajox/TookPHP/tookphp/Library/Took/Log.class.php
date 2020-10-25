<?php
/**
 * 日志处理类
 * @package     Core
 * @author      lajox <lajox@19www.com>
 */
namespace Took;
class Log
{
    // 日志级别
    const FATAL = 'FATAL'; // 严重错误: 导致系统崩溃无法使用
    const ERROR = 'ERROR'; // 一般错误: 一般性错误
    const WARNING = 'WARNING'; // 警告性错误: 需要发出警告的错误
    const NOTICE = 'NOTICE'; // 通知: 程序可以运行但是还不够完美的错误
    const INFO = 'INFO'; // 信息: 程序输出信息
    const DEBUG = 'DEBUG'; // 调试: 调试信息
    const SQL = 'SQL'; // SQL：SQL语句 注意只在调试模式开启时有效
    //日志信息
    static $log = array();

    /**
     * 记录日志内容
     * @param string $message 错误
     * @param string $level 级别
     * @param bool $record 是否记录
     */
    static public function record($message, $level = self::ERROR, $record = false)
    {
        if ($record || in_array($level, C('LOG_LEVEL'))) {
            self::$log[] = "{$level}: {$message}\r\n";
        }
    }

    /**
     * 存储日志内容
     * @access public
     * @param int $type 处理方式
     * @param string $destination 日志文件
     * @param string $extraHeaders 额外信息（发送邮件）
     * @return void
     */
    static public function save($type = 3, $destination = NULL, $extraHeaders = NULL)
    {
        $now = date(' c ');
        if (empty(self::$log)) return;
        if (is_null($destination)) {
            $destination = TEMP_LOG_PATH . date("Y_m_d") . ".log";
        }
        $log = implode("", self::$log);
        $log = "[{$now}] ".$_SERVER['REMOTE_ADDR'].' '.$_SERVER['REQUEST_URI']."\r\n{$log}\r\n";
        if (is_dir(TEMP_LOG_PATH)) error_log($log, $type, $destination, $extraHeaders);
        self::$log = array();
    }

    /**
     * 写入日志内容
     * @access public
     * @param string $message 日志内容
     * @param string $level 错误等级
     * @param int $type 处理方式
     * @param string $destination 日志文件
     * @param string $extraHeaders
     * @return void
     */
    static public function write($message, $level = self::ERROR, $type = 3, $destination = NULL, $extraHeaders = NULL)
    {
        if (is_null($destination)) {
            $destination = TEMP_LOG_PATH . date("Y_m_d") . ".log";
        }
        if (is_dir(TEMP_LOG_PATH)) error_log(date("[ c ]") . "{$level}: {$message}\r\n", $type, $destination, $extraHeaders);
    }

}