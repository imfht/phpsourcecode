<?php

namespace SgIoc\Cache;

/**
 * memcache存储引擎
 * User: freelife2020@163.com
 * Date: 2018/3/16
 * Time: 14:17
 */

class MemcacheStore extends StoreAbstract
{
    /**
     * 初使化
     * MemcachedStore constructor.
     * @param $memcache
     * @param array $config
     * @throws \Exception
     */
    public function __construct(\Memcache $memcache, $config = null)
    {
        $this->app = $memcache;
        if (!is_null($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * 获取详情
     * @return array
     */
    public function info()
    {
        return array(
            'link'     => $this->app,
            'config'   => $this->config,
            'stats'    => $this->app->getStats(),
            'stat_all' => $this->app->getExtendedStats(),
            'version'  => $this->app->getVersion(),
        );
    }

    /**
     * 获取实例
     * @return MemcacheConnector
     */
    public function getInstance()
    {
        return $this->app;
    }

    /**
     * 判断是否存在
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return $this->get($key) ? true : false;
    }

    /**
     * 获取&删除
     * @param $key
     * @return array|bool|string
     */
    public function pull($key)
    {
        $value = $this->get($key);
        $this->forget($key);
        return $value;
    }

    /**
     * 获取值,支持匿名函数
     * @param $key
     * @param bool $default 支持匿名函数
     * @return array|bool|string
     */
    public function get($key, $default = null)
    {
        $key = $this->getKey($key);
        $value = $this->app->get($key);
        if ($value !== false) {
            return $value;
        }
        return $this->value($default);
    }

    /**
     * 不存在则创建,成功返回true;存在则返回 false
     * @param $key
     * @param $value
     * @param $minutes
     * @return bool
     */
    public function add($key, $value, $minutes = null)
    {
        $key = $this->getKey($key);
        $second  = is_null($minutes) ? $this->config['expired'] : $minutes * 60;
        $expired = $second > 0 ? time() + $second : 0;
        $isZip   = $this->config['is_zip'] == 1 ? MEMCACHE_COMPRESSED : 0;
        $value   = $this->value($value);
        return $this->app->add($key, $value, $isZip, $expired);
    }

    /**
     * 设置和多个设置,存在则覆盖,不存在则创建,支持匿名函数
     * @param $key
     * @param $value
     * @param $minutes
     * @return bool
     */
    public function put($key, $value, $minutes = null)
    {
        $key     = $this->getKey($key);
        $second  = is_null($minutes) ? $this->config['expired'] : $minutes * 60;
        $expired = $second > 0 ? time() + $second : 0;
        $isZip   = $this->config['is_zip'] == 1 ? MEMCACHE_COMPRESSED : 0;
        $value   = $this->value($value);
        return $this->app->set($key, $value, $isZip, $expired);
    }

    /**
     * 永久存储
     * @param $key
     * @param $value
     * @return bool
     */
    public function forever($key, $value)
    {
        return $this->put($key, $value, 0);
    }

    /**
     * 递增
     * @param $key
     * @param int $value
     * @return array|bool|int|string
     */
    public function increment($key, $value = 1)
    {
        $key = $this->getKey($key);
        return $this->app->increment($key, $value);
    }

    /**
     * 递减
     * @param $key
     * @param int $value
     * @return array|bool|int|string
     */
    public function decrement($key, $value = 1)
    {
        $key = $this->getKey($key);
        return $this->app->decrement($key, $value);
    }

    /**
     * 删除键
     * @param $key
     * @return bool
     */
    public function forget($key)
    {
        $key = $this->getKey($key);
        if ($this->has($key)) {
            return $this->app->delete($key);
        }
        return false;
    }

    /**
     * 清理所有缓存
     * @return bool
     */
    public function flush()
    {
        return $this->app->flush();
    }

    /**
     * 存在则返回缓存,不存在则创建缓存并返回结果,支持匿名函数
     * @param $key
     * @param $minutes
     * @param mixed $callback
     * @return array|bool|mixed|string
     */
    public function remember($key, $minutes, $callback)
    {
        if (!is_null($value = $this->get($key))) {
            return $value;
        }
        $this->put($key, $value = $this->value($callback), $minutes);
        return $value;
    }

    /**
     * 永久缓存,支持匿名函数
     * @param $key
     * @param mixed $callback
     * @return array|bool|mixed|string
     */
    public function rememberForever($key, $callback)
    {
        return $this->remember($key, 0, $this->value($callback));
    }

    /**
     * 获取带前缀的键
     * @param $key
     * @return string
     */
    public function getKey($key)
    {
        return $this->config['preFix'] . $key;
    }
    public function close()
    {
        return $this->app->close();
    }

}