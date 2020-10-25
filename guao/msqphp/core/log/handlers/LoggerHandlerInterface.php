<?php declare (strict_types = 1);
namespace msqphp\core\log\handlers;

interface LoggerHandlerInterface
{
    /**
     * 配置参数
     *
     * @param array $config [description]
     */
    public function __construct(array $config);
    /**
     *
     * @param mixed $level
     * @param string $message
     * @return void
     */
    public function record(string $level, string $message, $context);
/*
public function emergency(string $message, $context);
public function alert(string $message, $context);
public function critical(string $message, $context);
public function error(string $message, $context);
public function warning(string $message, $context);
public function notice(string $message, $context);
public function info(string $message, $context);
public function debug(string $message, $context);
public function exception(string $message, $context);
public function success(string $message, $context);
 */
}
