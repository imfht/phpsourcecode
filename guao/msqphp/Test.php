<?php declare(strict_types = 1);
namespace msqphp;

final class Test
{
    private static function exception(string $message) : void
    {
        throw new \Exception($message);
    }
    public static function environmentCheck() : void
    {
        //版本检测
        version_compare(PHP_VERSION, '7.0.0', '>') || static::exception('require php 7.1, 版本低点可能也没什么');
        function_exists('mb_get_info') || static::exception('没有开启mb扩展, 可能也没什么');
        class_exists('PDO') || static::exception('没有开启Pdo扩展, 可能也没什么');
    }
    public static function run() : void
    {
        set_time_limit(300);
        require \msqphp\Environment::getPath('test') . 'test.php';
    }
}