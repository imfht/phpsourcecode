<?php

namespace SgIoc\Cache;
/**
 * Redis 引擎
 * User: freelife2020@163.com
 * Date: 2018/4/13
 * Time: 16:42
 */
class RedisStore extends StoreAbstract
{

    /**
     * 初使化
     * RedisConnector constructor.
     * @param $redis
     * @param array $config
     * @throws \Exception
     */
    public function __construct(\Redis $redis, $config = null)
    {
        $this->app = $redis;
        if (!is_null($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }

    public function info()
    {
        return array(
            'info' => $this->app->info(),
        );
    }

    public function getInstance()
    {
        return $this->app;
    }

    /**
     * 判断key是否存在
     * @param $key
     * @return mixed
     */
    public function has($key)
    {
        return $this->app->exists($key);
    }

    /**
     * 获取值
     * @param $key
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $this->value($default);
        }
        $data = $this->app->get($key);
        return $this->unserialize($data);
    }

    /**
     * 获取值并删除
     * @param $key
     * @return mixed
     */
    public function pull($key)
    {
        $data = $this->get($key);
        $this->forget($key);
        return $data;
    }

    /**
     * 添加值,如何存在则返回false,不存在则返回true
     * @param $key
     * @param $value
     * @param $minutes
     * @return bool|mixed
     */
    public function add($key, $value, $minutes)
    {
        if ($this->has($key)) {
            return false;
        }
        return $this->put($key, $value, $minutes);
    }

    /**
     * 设置结果,支持匿名函数,数组,对象
     * @param $key string 键
     * @param $value mixed 值
     * @param $minutes int 分钟
     * @return mixed
     */
    public function put($key, $value, $minutes = null)
    {
        $value  = $this->value($value);
        $second = is_null($minutes) ? $this->config['expired'] : $minutes * 60;
        $value  = $this->serialize($value);
        return $this->app->set($key, $value, $second);
    }

    /**
     * 递减
     * @param $key
     * @param int $value
     * @return mixed
     */
    public function decrement($key, $value = 1)
    {
        return $this->app->decr($key);
    }

    /**
     * 递增
     * @param $key
     * @param int $value
     * @return mixed
     */
    public function increment($key, $value = 1)
    {
        return $this->app->incr($key);
    }

    /**
     * 清除所有的键
     * @return mixed
     */
    public function flush()
    {
        return $this->app->flushAll();
    }

    /**
     * 删除一个键
     * @param $key
     * @return mixed
     */
    public function forget($key)
    {
        return $this->app->delete($key);
    }

    /**
     * 键如果存在,则返回数据,不存在,存储再返回数据
     * @param $key string 键
     * @param $minutes int 分钟
     * @param mixed $callback 值
     * @return bool|mixed
     */
    public function remember($key, $minutes, $callback)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }
        $value = $this->value($callback);
        $bool  = $this->put($key, $value, $minutes);
        if ($bool) {
            return $value;
        }
        return false;
    }

    /**
     * 永久存储
     * @param $key
     * @param $value
     * @return mixed
     */
    public function forever($key, $value)
    {
        return $this->put($key, $this->value($value), $this->config['forever_second']);
    }

    /**
     * 永久存储,如果存在,直接返回数据;不存在则存储后返回数据
     * @param $key
     * @param mixed $callback
     * @return bool|mixed
     */
    public function rememberForever($key, $callback)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }
        $value = $this->value($callback);
        $bool  = $this->forever($key, $value);
        if ($bool) {
            return $value;
        }
        return false;
    }

    public function getKey($key)
    {
        return $key;
    }

    public function close()
    {
        if ($this->app) {
            return $this->app->close();
        }
    }

    /**
     * 保存至磁盘上,
     * @return int 返回时间戳
     */
    public function save()
    {
        return $this->app->lastSave();
    }
}