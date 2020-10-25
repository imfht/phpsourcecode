<?php

namespace Rose1988c\RouteCache;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Cache\Repository as CacheRepository;

class BaseCacheMiddleware
{
    public $cache;
    public $config;
    
    /**
     * Class Constructor
     * @param CacheRepository $cache
     */
    public function __construct(CacheRepository $cache)
    {
        $this->cache  = $cache;
        $this->config = Config::get('routecache');
    }

    /**
     * Define is the cache has a particular key
     * @param  string  $key key to be searched for
     * @return boolean      true/false
     */
    public function has($key)
    {
        return $this->cache->has($key);
    }

    /**
     * Get the value of a particular key from the cache
     * @param  string  $key key to be fetched
     * @return string
     */
    public function get($key)
    {
        return $this->cache->get($key);
    }

    /**
     * forget key
     *
     * @param $key
     * @return bool
     */
    public function forget($key)
    {
        return $this->cache->forget($key);
    }

    /**
     * Store a key and value in cache for a particular amount of time
     * @param  string $key     Key to store by
     * @param  string $content Value to store
     * @param  integer $time    Amount in minutes to store it for
     * @return void
     */
    public function put($key, $content, $time)
    {
        return $this->cache->put($key, $content, $time);
    }

    /**
     * Check with config file if cache is enabled or not
     * @return bool true/fale
     */
    public function enabled()
    {
        return $this->config['enabled'];
    }

    /**
     * Generate a key for the cache
     * @param  string $url utl to cache
     * @return string     generated key
     */
    protected function keygen($url)
    {
        return 'route_' . str_slug($url);
    }
}
