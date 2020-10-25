<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/30
 * Time: 22:35
 */

namespace fastwork\facades;


use fastwork\Facade;

/**
 * @see \fastwork\Cookie
 * @mixin \fastwork\Cookie
 * @method void init(array $config = []) static 初始化
 * @method bool has(string $name,string $prefix = null) static 判断Cookie数据
 * @method mixed prefix(string $prefix = '') static 设置或者获取cookie作用域（前缀）
 * @method mixed get(string $name,string $prefix = null) static Cookie获取
 * @method mixed set(string $name, mixed $value = null, mixed $option = null) static 设置Cookie
 * @method void forever(string $name, mixed $value = null, mixed $option = null) static 永久保存Cookie数据
 * @method void delete(string $name, string $prefix = null) static Cookie删除
 * @method void clear($prefix = null) static Cookie清空
 */
class Cookie extends Facade
{
    protected static function getFacadeClass(): string
    {
        return 'cookie';
    }
}