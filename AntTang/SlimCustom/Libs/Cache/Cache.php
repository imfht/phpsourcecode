<?php
/**
 * @package     Cache.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年5月17日
 */

namespace SlimCustom\Libs\Cache;

use SlimCustom\Libs\Contracts\Cache\Store;
use SlimCustom\Libs\Support\Arr;
use SlimCustom\Libs\App;

/**
 * Cache
 * 
 * @author Jing <tangjing3321@gmail.com>
 */
class Cache implements Store
{
    
    /**
     * 缓存链接对象
     * 
     * @var \SlimCustom\Libs\Contracts\Cache\Store
     */
    public $connection;
    
    /**
     * 支持的缓存的驱动
     * 
     * @var array
     */
    protected $support = [
        'redis' => \SlimCustom\Libs\Cache\Redis::class,
        'file' => \SlimCustom\Libs\Cache\File::class,
    ];
    
    /**
     * 前缀
     * 
     * @var string
     */
    protected $prefix;

    /**
     * 初始化缓存
     *
     * @param string | object $connection
     */
    public function __construct($connection = null)
    {
        $this->setPrefix(config('cache.prefix', ''));
        $this->setConnection($connection);
    }
    
    /**
     * Retrieve an item from the cache by key.
     *
     * @param string $key            
     * @param mix $default            
     * @return mixed
     */
    public function get($key, $default = null)
    {
        list($key, $childKey) = parseKey($key);
        $value = $this->connection->get($this->prefix . $key);
        if (! is_null($value) && $value !== false) {
            $value = is_numeric($value) ? $value : unserialize($value);
            if ($childKey) {
                $value = Arr::get($value, $childKey, $default);
            }
            if (is_null($value)) {
                return value($default);
            }
            return $value;
        }
        else {
            return value($default);
        }
    }

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param string $key            
     * @param mixed $value            
     * @param int $minutes            
     * @return void
     */
    public function put($key, $value, $minutes)
    {
        list($firstKey, $childKey) = parseKey($key);
        $res = $this->get($firstKey, []);
        if (is_array($res) && $childKey) {
            $value = Arr::set($res, $childKey, $value);
            $key = $firstKey;
        }
        $value = is_numeric($value) ? $value : serialize($value);
        $minutes = max(1, $minutes);
        if (! $this->connection->put($this->prefix . $key, $value, $minutes * 60)) {
            return false;
        }
        return true;
    }
    
    /**
     * Remove
     * 
     * @support key.key
     * @param string $key
     * @return void|boolean
     */
    public function remove($key)
    {
        list($firstKey, $childKey) = parseKey($key);
        if (! is_null($childKey)) {
            return $this->put($key, null, 1);
        }
        return $this->forget($firstKey);
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param string $key            
     * @param mixed $value            
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
        return $this->connection->increment($this->prefix . $key, $value);
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param string $key            
     * @param mixed $value            
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        return $this->connection->decrement($this->prefix . $key, $value);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param string $key            
     * @param mixed $value            
     * @return void
     */
    public function forever($key, $value)
    {
        list($firstKey, $childKey) = parseKey($key);
        $res = $this->get($firstKey, []);
        if (is_array($res) && $childKey) {
            $value = Arr::add($res, $childKey, $value);
            $key = $firstKey;
        }
        $value = is_numeric($value) ? $value : serialize($value);
        return $this->connection->forever($this->prefix . $key, $value);
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key            
     * @return bool
     */
    public function forget($key)
    {
        return (bool) $this->connection->forget($this->prefix . $key);
    }
    
    /**
     * Remove all items from the cache.
     *
     * @return void
     */
    public function flush()
    {
        return $this->connection->flush();
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }
    
    /**
     * Set the cache key prefix.
     *
     * @param  string  $prefix
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = ! empty($prefix) ? $prefix.':' : '';
    }
    
    /**
     * Get cache server object
     * 
     * @return \SlimCustom\Libs\Contracts\Cache\Store
     */
    public function connection()
    {
        return $this->connection;
    }

    /**
     * Set the connection name to be used.
     *
     * @param string $connection            
     * @return void
     */
    public function setConnection($connection)
    {
        $connection = $connection ? $connection : config('cache.default', 'file');
        if (is_string($connection)) {
            if (! isset($this->support[$connection])) {
                throw new \Exception('not support: ' . $connection);
            }
            $connection = App::di($this->support[$connection]);
        }
        $this->connection = $connection;
    }
    
    /**
     * __call
     * 
     * @param string $method
     * @param array $args
     * @throws \BadMethodCallException
     * @return mixed
     */
    public function __call($method, $args = [])
    {
        if (! is_callable([$this->connection, $method])) {
            throw new \BadMethodCallException('Can not callbale method on this connection');
        }
        return call_user_func_array([$this->connection, $method], $args);
    }
    
    /**
     * __callstatic
     * 
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public static function __callstatic($method, $args = [])
    {
        return call_user_func_array([
            App::single('cache'),
            $method
        ], $args);
    }
    
    /**
     * 关闭资源
     */
    public function __destruct()
    {
        if ($this->connection()) {
            $this->connection()->close();
        }
    }
}