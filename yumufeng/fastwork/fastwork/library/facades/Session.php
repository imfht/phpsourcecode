<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/2
 * Time: 16:24
 */

namespace fastwork\facades;


use fastwork\Facade;
/**
 * @see \fastwork\Session
 * @mixin \fastwork\Session
 * @method void init(array $config = []) static session初始化
 * @method bool has(string $name,string $prefix = null) static 判断session数据
 * @method mixed get(string $name = '',string $prefix = null) static session获取
 * @method mixed pull(string $name,string $prefix = null) static session获取并删除
 * @method void push(string $key, mixed $value) static 添加数据到一个session数组
 * @method void set(string $name, mixed $value , string $prefix = null) static 设置session数据
 * @method void delete(string $name, string $prefix = null) static 删除session数据
 * @method void clear($prefix = null) static 清空session数据
 * @method void start() static 启动session
 * @method void destroy() static 销毁session
 * @method void pause() static 暂停session
 * @method void regenerate(bool $delete = false) static 重新生成session_id
 */
class Session extends Facade
{
    protected static function getFacadeClass()
    {
        return 'session';
    }

}