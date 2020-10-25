<?php
/**
 * Redis 实现抽象类
 * */
 
 abstract class Cache_RedisCache
 {
    protected $_expire;

	protected $_err_code;
	protected $_err_info;
    
    protected static $_cache_pool;
    protected $_redis;
    
    protected $_cache_key;
    
    public function __construct()
    {
        $this->_err_info = '';
        $this->_err_code = 1; //SUCCESS 1
        $this->_expire = -1;
        
        //设置key
        $this->setCacheKey();
        
        //打开Redis连接
        $this->_openCacheConn();
    }
    
    // 设置key
    abstract protected function setCacheKey();
    
    public function getCacheKey()
    {
        return $this->_cache_key;
    }
    
    /**
	 * 设定过期的秒速
	 * @param int $seconds
	 */
    public function setExpire($seconds)
    {
        $this->_expire = $seconds;
    }
    
    public function getExpire()
    {
        return $this->_expire;
    }   
    
    //打开Redis连接
    protected function _openCacheConn()
	{
		if (empty(self::$_cache_pool))
        {
            self::$_cache_pool = new Cache_RedisPool();
        }
        $this->_redis = self::$_cache_pool->getConnection();
    }
    
    //关闭
    protected function _closeCacheConn()
    {
        
    }
    
    //判断key是否存在
    public function isExists()
    {
        return $this->_redis->exists($this->_cache_key);
    }
    
    //获取缓存数据类型
    public function getType()
    {
        return $this->_redis->type($this->_cache_key);
    }
    
    //设置超时时间
    public function setTimeout()
    {
        if ($this->_expire > 0)
        {
            $this->_redis->setTimeout($this->_cache_key, $this->_expire);
        }
    }
    
    //指定具体日期时间进行过期
    public function setTimeoutByDateTime($datetime)
    {
        $time = strtotime($datetime);
        $this->_redis->expireAt($this->_cache_key,  $time);
    }
    
    //获取剩余的有效秒数
    public function getTTL()
    {
        return $this->_redis->ttl($this->_cache_key);
    }
    
    //删除缓存
    public function delete()
    {
        $this->_redis->delete($this->_cache_key);
    }
 }
?>