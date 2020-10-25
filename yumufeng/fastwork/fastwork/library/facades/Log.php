<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/31
 * Time: 12:53
 */

namespace fastwork\facades;


use fastwork\Facade;

/**
 * @see \fastwork\Log
 * @mixin \fastwork\Log
 * @method \fastwork\Log record(string $type = 'info', mixed $msg) static 记录日志信息
 * @method bool save() static 保存调试信息
 * @method void log(string $level, mixed $message) static 记录日志信息
 * @method void emergency(mixed $message) static 记录emergency信息
 * @method void alert(mixed $message) static 记录alert信息
 * @method void critical(mixed $message) static 记录critical信息
 * @method void error(mixed $message) static 记录error信息
 * @method void warning(mixed $message) static 记录warning信息
 * @method void notice(mixed $message) static 记录notice信息
 * @method void info(mixed $message) static 记录info信息
 * @method void debug(mixed $message) static 记录debug信息
 * @method void sql(mixed $message) static 记录sql信息
 */
class Log extends Facade
{

    protected static function getFacadeClass(): string
    {
        return 'log';
    }
}