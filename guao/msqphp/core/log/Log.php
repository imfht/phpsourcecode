<?php declare (strict_types = 1);
namespace msqphp\core\log;

use msqphp\core\config\Config;

final class Log
{
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';
    const EXCEPTION = 'exception';
    const SUCCESS   = 'success';

    private static $handler = null;
    private static $config  = [];

    private static function exception(string $message): void
    {
        throw new LogException('[日志异常]' . $message);
    }

    public static function record(string $level, string $messages, array $context = []): void
    {
        $handler = static::getHandler();
        if (in_array(strtolower($level), static::$config['level'])) {
            $handler->record($level, $messages, $context);
        }
    }

    // 初始化处理器
    private static function getHandler(): handlers\LoggerHandlerInterface
    {
        if (static::$handler === null) {

            // 初始化配置
            static::$config = $config = array_merge(static::$config, Config::get('log'));
            // 连接处理器文件路径
            $file = \msqphp\Environment::getVenderFilePath(__CLASS__, $config['default_handler'], 'handlers');
            is_file($file) || static::exception($config['default_handler'] . '日志处理类不存在');
            // 加载处理类文件
            require $file;
            // 拼接类名
            $class = __NAMESPACE__ . '\\handlers\\' . $config['default_handler'];
            // 创建类
            static::$handler = new $class($config['handlers_config'][$config['default_handler']]);
        }

        return static::$handler;
    }
}
