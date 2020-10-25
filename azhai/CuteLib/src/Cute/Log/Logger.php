<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Log;
\app()->import('Psr\\Log', VENDOR_ROOT . '/log');

use \Cute\Log\LoggerInterface;
use \Cute\Log\LogLevel;
use \Cute\Utility\IP;
use \Cute\Utility\Word;


/**
 * 日志
 */
abstract class Logger implements LoggerInterface
{
    const EMERGENCY = 1;
    const ALERT = 2;
    const CRITICAL = 3;
    const ERROR = 4;
    const WARNING = 5;
    const NOTICE = 6;
    const INFO = 7;
    const DEBUG = 8;

    protected $threshold = 0;

    /**
     * 构造函数，设置过滤级别
     * @param string $threshold 过滤级别（低于本级别的不记录）
     */
    public function __construct($level = 'DEBUG')
    {
        $this->threshold = self::getLevel($level);
    }

    public static function getLevel($level)
    {
        return constant(__CLASS__ . '::' . strtoupper($level));
    }

    /**
     * 比较两个过滤级别的重要程度
     * @param int $level 消息级别
     * @return bool 消息级别持平或更重要
     */
    public function allowLevel($level)
    {
        if ($level_value = self::getLevel($level)) {
            return $level_value <= $this->threshold;
        }
    }

    public static function getClientIP()
    {
        return IP::getClientIP();
    }

    public static function format($message, array $context = [])
    {
        $content = is_null($message) ? '' : (string)$message;
        return Word::replaceWith($content, $context, '{', '}');
    }

    /**
     * System is unusable.
     */
    public function emergency($message, array $context = [])
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     */
    public function alert($message, array $context = [])
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     */
    public function critical($message, array $context = [])
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     */
    public function error($message, array $context = [])
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     */
    public function warning($message, array $context = [])
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     */
    public function notice($message, array $context = [])
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     */
    public function info($message, array $context = [])
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     */
    public function debug($message, array $context = [])
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    abstract public function log($level, $message, array $context = []);
}
