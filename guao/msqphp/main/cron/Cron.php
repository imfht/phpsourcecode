<?php declare (strict_types = 1);
namespace msqphp\main\cron;

use msqphp\base\file\File;

final class Cron
{
    use CronUpdateTrait, CronRunTrait;

    // 定时任务缓存目录
    private static $path = '';

    private static function exception(string $message): void
    {
        throw new CronException($message);
    }

    /**
     * 获得对应路径
     *
     * @param   string  $type  文件类别
     *
     * @return  string
     */
    public static function getFilePath(string $type = ''): string
    {
        // 为空赋值
        static::$path === '' && static::$path = \msqphp\Environment::getPath('storage') . 'framework' . DIRECTORY_SEPARATOR . 'cron' . DIRECTORY_SEPARATOR;
        // 判断取值
        switch ($type) {
            case '':
                return static::$path;
            case 'pid':
                return static::$path . 'pid.txt';
            case 'info':
                return static::$path . 'info.php';
            case 'log':
                return static::$path . date('Ymd') . DIRECTORY_SEPARATOR . 'log.txt';
            case 'next_run_time':
                return static::$path . 'next_run_time.txt';
            case 'cache':
                return static::$path . 'cache.txt';
            case 'cache_middle':
                return static::$path . 'cache_middle.txt';
            case 'lock':
                return static::$path . 'lock';
            case 'cron':
                return \msqphp\Environment::getPath('application') . 'cron';
            default:
                static::exception('错误的类型' . $type);
        }
    }

    // 日志写入
    public static function recordLog(string $message): void
    {
        try {
            File::append(static::getFilePath('log'), $message . PHP_EOL, true);
        } catch (FileException $e) {
            throw new CronException('定时任务日志记录出错,错误原因:' . $e->getMessage());
        }
    }
}
