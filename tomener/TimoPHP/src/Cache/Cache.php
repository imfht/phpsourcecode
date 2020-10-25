<?php
/* --------------------------------------------
 * TimoPHP a Fast Simple Smart PHP FrameWork  |
 * Author: Tommy 863758705@qq.com             |
 * Link: http://www.TimoPHP.com/              |
 * Since: 2016                                |
 * --------------------------------------------
 */

namespace Timo\Cache;


use Timo\Config\Config;

/**
 * Class Cache
 *
 * @method static mixed get() options(string $key, mixed $value)
 * @method static mixed get() get(string $name)
 * @method static bool set() set(string $name, mixed $value, mixed $expire = null)
 * @method static bool rm() rm(string $name, bool $expire = false)
 * @method static bool clear() clear()
 */
class Cache
{
    protected static $instance = [];
    public static $readTimes   = 0;
    public static $writeTimes  = 0;

    /**
     * 操作句柄
     *
     * @var object
     */
    protected static $handler = null;

    /**
     * 连接缓存
     *
     * @param array $options 配置数组
     * @return object
     */
    public static function connect(array $options = [])
    {
        $md5 = md5(serialize($options));
        if (!isset(static::$instance[$md5])) {
            $type  = !empty($options['type']) ? $options['type'] : 'File';
            $class = (!empty($options['namespace']) ? $options['namespace'] : '\\Timo\\Cache\\') . ucwords($type);
            unset($options['type']);
            static::$instance[$md5] = new $class($options);
        }
        static::$handler = static::$instance[$md5];
        return static::$handler;
    }

    public static function __callStatic($method, $params)
    {
        if (is_null(static::$handler)) {
            // 自动初始化缓存
            static::connect(Config::runtime('cache'));
        }
        return call_user_func_array([self::$handler, $method], $params);
    }
}
