<?php
class Cache_RedisString extends Cache_RedisCache
{
    public $cache_value;
    public $cache_key;
  
    public function __construct($key,$time = 7200)
    {
        $this->cache_key = $key;
        $this->setExpire($time);
        parent::__construct();
    }
    
    public function get()
    {
        if ($this->isExists())
        {
            $this->cache_value = $this->_redis->get($this->_cache_key);
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function set()
    {
        $this->_redis->set($this->_cache_key, $this->cache_value);
    }
    
    public function setCacheKey()
    {
        $this->_chache_key = $this->cache_key;
    }
}
?>