<?php declare (strict_types = 1);
namespace msqphp\core\log\handlers;

use msqphp\base;

class File implements LoggerHandlerInterface
{
    private $config = [
        'path'        => '',
        'max_size'    => 2097152,
        'files'       => 1,
        'date_format' => 'Y-m-d H:i:s',
        'deep'        => 0,
        'extension'   => '.log',
    ];

    public function __construct(array $config)
    {
        $config = array_merge($this->config, $config);

        if (!is_dir($config['path'])) {
            throw new LoggerHandlerException('日志目录不存在');
        }
        if (!is_writable($config['path'])) {
            throw new LoggerHandlerException('日志目录不可写');
        }

        $config['path'] = realpath($config['path']) . DIRECTORY_SEPARATOR;

        $this->config = $config;
    }

    public function record(string $level, string $message, $context = null)
    {
        base\file\File::append(
            $this->config['path'] . (APP_DEBUG ? 'debug' . DIRECTORY_SEPARATOR : '') . date('Y-m-d') . DIRECTORY_SEPARATOR . $level . random_int(1, $this->config['files']) . $this->config['extension']
            , '[' . date($this->config['date_format']) . ']' . $level . ':' . $message . PHP_EOL
            , true
        );
    }
    public function emergency(string $message, $context = null)
    {
        static::record('emergency', $message, $context);
    }
    public function alert(string $message, $context = null)
    {
        static::record('alert', $message, $context);
    }
    public function critical(string $message, $context = null)
    {
        static::record('critical', $message, $context);
    }
    public function error(string $message, $context = null)
    {
        static::record('error', $message, $context);
    }
    public function warning(string $message, $context = null)
    {
        static::record('warning', $message, $context);
    }
    public function notice(string $message, $context = null)
    {
        static::record('notice', $message, $context);
    }
    public function info(string $message, $context = null)
    {
        static::record('info', $message, $context);
    }
    public function debug(string $message, $context = null)
    {
        static::record('debug', $message, $context);
    }
    public function exception(string $message, $context = null)
    {
        static::record('exception', $message, $context);
    }
    public function success(string $message, $context = null)
    {
        static::record('success', $message, $context);
    }
}
