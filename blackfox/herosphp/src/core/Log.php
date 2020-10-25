<?php
/**
 * 日志类
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2016-11-16 v1.3.0
 */

namespace herosphp\core;

use herosphp\exception\HeroException;
use herosphp\files\FileUtils;
use herosphp\string\StringUtils;

class Log {

    //提示信息日志
    const LOG_INFO = '.info.log';
    //错误信息日志
    const LOG_ERROR = '.error.log';

    /**
     * 记录提示信息日志
     * @param $message
     * @param $logFile 日志文件名称，如果没有指定则记录到默认的日志文件
     * @return int
     */
    public static function info($message, $logFile='') {

        if ( $message instanceof HeroException ) $message = $message->toString();

        if ( is_object($message) ) $message = serialize($message);

        if ( is_array($message) ) $message = StringUtils::jsonEncode($message);

        $logDir = APP_RUNTIME_PATH . 'logs/'.date('Y').'/'.date('m').'/';
        if ( !file_exists($logDir) ) FileUtils::makeFileDirs($logDir);
        return file_put_contents($logDir.date("Y-m-d").'-'.$logFile.self::LOG_INFO, '['.date('Y-m-d H:i:s').'] '.$message."\n", FILE_APPEND);
    }

    /**
     * 记录错误信息日志
     * @param $message
     * @param $logFile 日志文件名称，如果没有指定则记录到默认的日志文件
     * @return int
     */
    public static function error($message, $logFile='') {

        if ( $message instanceof HeroException ) $message = $message->toString();

        if ( is_array($message) ) $message = StringUtils::jsonEncode($message);

        $logDir = APP_RUNTIME_PATH . 'logs/'.date('Y').'/'.date('m').'/';
        if ( !file_exists($logDir) ) FileUtils::makeFileDirs($logDir);
        return file_put_contents($logDir.date("Y-m-d").$logFile.self::LOG_ERROR, '['.date('Y-m-d H:i:s').'] '.$message."\n", FILE_APPEND);
    }
}