<?php
/**
 * @package     Redis.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年5月17日
 */

namespace SlimCustom\Libs\Cache;

use \Redis as PhpRedis;
use SlimCustom\Libs\Contracts\Cache\Store;

/**
 * Redis
 * 
 * @author Jing <tangjing3321@gmail.com>
 */
class Redis extends PhpRedis implements Store
{
    /**
     * 初始化redis
     * 
     * @param array $config
     * @throws \Exception
     */
    public function __construct($config = [])
    {
        if (! extension_loaded('redis')) {
            throw new \Exception('not support: redis');
        }
        parent::__construct();
        $this->connectRedis($config ? $config : config('cache.stores.redis'));
    }
    
    /**
     * 连接redis服务器, 暂不支持集群
     * 
     * @param array $config
     * @throws \ErrorException
     */
    public function connectRedis($config = [])
    {
        if (! $config) {
            throw new \ErrorException('no connect params');
        }
        // 连接参数
        $host = $config['servers'][0]['host'];
        $port = $config['servers'][0]['port'];
        $timeout = isset($config['servers'][0]['timeout']) ? $config['servers'][0]['timeout'] : 5;
        $password = isset($config['servers'][0]['password']) ? $config['servers'][0]['password'] : '';
        $persistent = isset($config['servers'][0]['persistent']) ? $config['servers'][0]['persistent'] : false;
        $database = isset($config['servers'][0]['database']) ? $config['servers'][0]['database'] : 0;
        // 连接
        $connectMethod = $persistent ? 'pconnect' : 'connect';
        $this->$connectMethod($host, $port, $timeout);
        // 用户验证
        ! $password ?: $this->auth($password);
        // 选择数据库
        ! $database ?: $this->select($database);
    }
    
    /**
     * Retrieve an item from the cache by key
     * 
     * {@inheritDoc}
     * @see Redis::get()
     */
    public function get($key)
    {
        return parent::get($key);
    }
    
    /**
     * Store an item in the cache for a given number of minutes.
     * 
     * @param string $key
     * @param string $value
     * @param integer $second
     */
    public function put($key, $value, $second)
    {
        return $this->setex($key, $second, $value);
    }
    
    /**
     * Increment the value of an item in the cache.
     * 
     * {@inheritDoc}
     * @see \SlimCustom\Libs\Contracts\Cache\Store::increment()
     */
    public function increment($key, $value = 1)
    {
        return $this->incrBy($$key, $value);
    }
    
    /**
     * Decrement the value of an item in the cache.
     * 
     * {@inheritDoc}
     * @see \SlimCustom\Libs\Contracts\Cache\Store::decrement()
     */
    public function decrement($key, $value = 1)
    {
        return $this->decrBy($key, $value);
    }
    
    /**
     * Store an item in the cache indefinitely.
     * 
     * {@inheritDoc}
     * @see \SlimCustom\Libs\Contracts\Cache\Store::forever()
     */
    public function forever($key, $value)
    {
        return $this->set($key, $value);
    }
    
    /**
     * Remove an item from the cache.
     * 
     * {@inheritDoc}
     * @see \SlimCustom\Libs\Contracts\Cache\Store::forget()
     */
    public function forget($key)
    {
        return (bool) $this->del($key);
    }
    
    /**
     * Remove all items from the cache.
     * 
     * {@inheritDoc}
     * @see \SlimCustom\Libs\Contracts\Cache\Store::flush()
     */
    public function flush()
    {
        return $this->flushDB();
    }
}