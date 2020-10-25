<?php
/**
 * Email:739800600@qq.com
 * User: 七觞酒
 * Date: 2014/10/7
 */
namespace framework\cache;
use framework\core\Abnormal;

class CacheRedis extends AbstractCache
{
    /** @var  \Redis */
    private $_redis;

    private $_config = array(
        'host'=>'127.0.0.1',
        'port'=>11211,
        'expire'=>0,
        'flag'=>0
    );
    public function __get($name)
    {
        if(isset($this->_config[$name])){
            return $this->_config[$name];
        }
        return null;
    }
    public function __construct(array $config)
    {
        if(!extension_loaded('redis')){
            throw new Abnormal('redis 扩展尚未安装', 500);
        }
        $this->_config = array_merge($this->_config, $config);
        $this->_redis = new \Redis();
        $this->_redis->connect($this->host, $this->port);
    }
    /**
     * 获取缓存内容
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->_redis->get($this->buildKey($key));
    }

    /**
     * 设置缓存
     *
     * @param $key
     * @param $value
     * @param null $expire
     * @return mixed
     */
    public function set($key, $value, $expire = null)
    {
        $key = $this->buildKey($key);
        $this->_redis->set($key, $value);
        if(empty($expire)) {
            $expire = $this->expire;
        }
        $this->_redis->expire($key, $expire);
        return true;
    }

    /**
     * 清除缓存
     * $name 为空时清除全部缓存
     * @param string $key
     * @return mixed
     */
    public function clean($key = null)
    {
        if(empty($key)) {
            return $this->_redis->flushDB();
        } else{
            return $this->_redis->del($this->buildKey($key));
        }
    }
}