<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/2
 * Time: 13:05
 */

namespace fastwork\facades;


use fastwork\Facade;

/**
 * Class Cache
 * @package fastwork\facades
 * @see \fastwork\Cache
 *
 * @method mixed setDefer($status = true) static 设置defer
 *
 * @method bool has(string $name) static 判断缓存是否存在
 * @method mixed get(string $name, mixed $default = false) static 读取缓存
 * @method mixed pull(string $name) static 读取缓存并删除
 * @method mixed set(string $name, mixed $value, int $expire = null) static 设置缓存
 * @method mixed inc(string $name, int $step = 1) static 自增缓存（针对数值缓存）
 * @method mixed dec(string $name, int $step = 1) static 自减缓存（针对数值缓存）
 * @method bool rm(string $name) static 删除缓存
 * @method bool clear(string $tag = null) static 清除缓存
 */
class Cache extends Facade
{

    protected static function getFacadeClass()
    {
        return 'cache';
    }

}