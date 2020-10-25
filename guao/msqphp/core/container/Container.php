<?php declare (strict_types = 1);
namespace msqphp\core\container;

/**
 * 容器,通过文件闭包或者人工设置实现
 * 快键使用app($name) // app('session')
 */
final class Container
{
    // 已经绑定的服务列表
    private static $bindings = [];

    // 已经实例化的服务
    private static $instances = [];

    private static function exception(string $message): void
    {
        throw new ContainerException($message);
    }

    public static function get(string $name, array $params = [])
    {
        //已存在,直接返回
        if (isset(static::$instances[$name])) {
            return static::$instances[$name];
        }
        //是否已绑定
        if (!isset(static::$bindings[$name])) {
            // 对应文件是否存在
            $file = \msqphp\Environment::getVenderFilePath(__CLASS__, $name, 'binds');
            $file === null && static::exception($name . '并不存在于容器中');
            $info = require $file;
            // 分享复制当实例集合中,否则直接返回
            static::set($name, $info['object'], $info['shared']);
            // 重新获取
            return static::get($name, $params);
        }
        return static::createInstance($name, $params);
    }

    private static function createInstance(string $name, array $params = [])
    {
        $concrete = static::$bindings[$name]['class']; //对象具体注册内容

        // 闭包函数
        if ($concrete instanceof \Closure) {
            $object = call_user_func_array($concrete, $params);
            //匿名函数方式
        } elseif (is_object($concrete)) {
            $object = $concrete;
            //字符串方式
        } elseif (is_string($concrete)) {
            $object = empty($params) ? new $concrete : call_user_func_array([new \ReflectionClass($concrete), 'newInstanceArgs'], $params);
        } else {
            $object = null;
        }
        //如果是共享服务，则写入_instances列表，下次直接取回
        static::$bindings[$name]['shared'] === true && static::$instances[$name] = $object;
        return $object;
    }

    public static function exists(string $name): bool
    {
        return isset(static::$bindings[$name]) || isset(static::$instances[$name]);
    }

    public static function delete(string $name): void
    {
        unset(static::$bindings[$name], static::$instances[$name]);
    }

    //设置服务
    public static function set(string $name, $class, bool $shared = false): void
    {
        static::delete($name);
        static::$bindings[$name] = ['class' => $class, 'shared' => $shared];
    }
}
