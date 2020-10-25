<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/1
 * Time: 19:22
 */

namespace fastwork\facades;

use fastwork\Facade;

/**
 * @see \fastwork\Config
 * @mixin \fastwork\Config
 * @method bool has(string $name) static 检测配置是否存在
 * @method array pull(string $name) static 获取一级配置
 * @method mixed get(string $name,mixed $default = null) static 获取配置参数
 * @method mixed set(string $name, mixed $value = null) static 设置配置参数
 * @method array reset(string $prefix ='') static 重置配置参数
 */
class Config extends Facade
{

    protected static function getFacadeClass(): string
    {
        return 'config';
    }
}