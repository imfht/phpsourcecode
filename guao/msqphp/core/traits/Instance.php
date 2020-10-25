<?php declare (strict_types = 1);
namespace msqphp\core\traits;

trait Instance
{
    private static $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        return static::$instance = static::$instance ?? new static();
    }

    public static function unsetInstance(): void
    {
        static::$instance = null;
    }

    private function __clone()
    {
        throw new TraitsException('单例对象无法克隆');
    }

    private function __wakeup()
    {
        throw new TraitsException('单例对象无法唤醒');
    }
}
