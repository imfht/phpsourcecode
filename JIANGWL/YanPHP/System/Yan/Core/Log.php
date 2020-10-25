<?php
/**
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 * Date: 2017/8/23
 * Time: 19:43
 */

namespace Yan\Core;


use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\ProcessIdProcessor;

/**
 * Class Log
 * @package Yan\Core
 * @method static bool emergency($message, array $context = array())
 * @method static bool alert($message, array $context = array())
 * @method static bool critical($message, array $context = array())
 * @method static bool error($message, array $context = array())
 * @method static bool warning($message, array $context = array())
 * @method static bool notice($message, array $context = array())
 * @method static bool info($message, array $context = array())
 * @method static bool debug($message, array $context = array())
 * @method static bool log($level, $message, array $context = array())
 */
class Log
{
    /**
     * @var Logger
     */
    protected static $logger = null;

    protected static $logPath = BASE_PATH . "/logs/server.log";

    protected static $logMaxFile = 0;

    protected static $logLevel = 'INFO';

    protected static $logFormat = "[%datetime%]-%extra.process_id% %channel%.%level_name%: %message% %context%\n";

    public static function getInstance(): Logger
    {
        if (empty(self::$logger)) {
            self::$logPath = Config::get('log_path') ?: self::$logPath;
            self::$logMaxFile = Config::get('log_max_file') ?: self::$logMaxFile;
            self::$logLevel = Config::get('log_level') ?: self::$logPath;
            self::$logFormat = Config::get('log_format') ?: self::$logFormat;

            $formatter = new LineFormatter(self::$logFormat);
            $handler = new RotatingFileHandler(self::$logPath, self::$logMaxFile, self::$logLevel);
            $handler->setFormatter($formatter);
            self::$logger = new Logger('YanLogger', [$handler], [new ProcessIdProcessor()]);
        }
        return self::$logger;
    }

    public static function initialize(): Logger
    {
        return self::getInstance();
    }

    /**
     * @param string $method
     * @param array $args
     * @return bool
     */
    public static function __callStatic(string $method, array $args): bool
    {
        if (method_exists(Logger::class, $method)) {
            return call_user_func_array([static::$logger, $method], $args);
        } else {
            throwErr("log method '{$method}' does not exist", ReturnCode::METHOD_NOT_EXIST, Exception\BadMethodCallException::class);
            return false;
        }
    }

    /**
     * @param string $method
     * @param array $args
     * @return bool
     */
    public function __call(string $method, array $args): bool
    {
        if (method_exists(Logger::class, $method)) {
            return call_user_func_array([static::$logger, $method], $args);
        } else {
            throwErr("log method '{$method}' does not exist", ReturnCode::METHOD_NOT_EXIST, Exception\BadMethodCallException::class);
            return false;
        }
    }
}