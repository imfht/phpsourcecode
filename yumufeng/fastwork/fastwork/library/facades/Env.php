<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/30
 * Time: 15:47
 */

namespace fastwork\facades;

use fastwork\Facade;

/**
 * @see \fastwork\Env
 * @mixin \fastwork\Env
 * @method void load(string $file) static 读取环境变量定义文件
 * @method mixed get(string $name = null, mixed $default = null) static 获取环境变量值
 * @method void set(mixed $env, string $value = null) static 设置环境变量值
 */
class Env extends Facade
{
    protected static function getFacadeClass(): string
    {
        return 'env';
    }
}