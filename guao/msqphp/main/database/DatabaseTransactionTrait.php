<?php declare(strict_types = 1);
namespace msqphp\main\database;

trait DatabaseTransactionTrait
{
    // 开始事物
    public static function beginTransaction($handler) : void
    {
        static::getHandler($handler)->beginTransaction();
    }

    // 提交事物
    public static function commit($handler) : void
    {
        static::getHandler($handler)->commit();
    }

    // 回滚事物
    public static function rollBack($handler) : void
    {
        static::getHandler($handler)->rollBack();
    }
}