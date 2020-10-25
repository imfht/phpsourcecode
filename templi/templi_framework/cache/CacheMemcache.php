<?php
/**
 * memcache缓存类
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date  2013-3-22
 */
namespace framework\cache;
use framework\core\Abnormal;

class CacheMemcache extends AbstractCache
{
    /**
     * @var \Memcache
     */
    private $_memcache;

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
        if(!extension_loaded('memcache')){
            throw new Abnormal('memcache 扩展尚未安装', 500);
        }
        $this->_config = array_merge($this->_config, $config);
        $this->_memcache = new \Memcache();
        $this->_memcache->connect($this->host, $this->port);
    }
    /**
     * 获取缓存
     * @param string $key  memcache id
     * @param boole
     * @return mixed
     */
    public function get($key){

        $value = $this->_memcache->get($this->buildKey($key), $this->_flag);
		return $value;
    }
    /**
     * 设置缓存
     *
     * @param string $key memecache id
     * @param mixed $value 缓存值
     * @param int $expire 有效期
     * @param boole
     * @return bool
     */
    public function set($key, $value, $expire=null){
        if (is_null($expire)) {
            $expire = $this->_expire;
        }
        return $this->_memcache->set($this->buildKey($key), $value, $this->_flag, $expire);
    }
    /**
     * 清除缓存
     *
     * @param string $key  memcache id
     * @return bool
     */
    public function clean($key=null){
        if($key){
            return $this->_memcache->delete($this->buildKey($key));
        }else{
            $this->_memcache->flush();
            return true;
        }
    }

    /**
     * @param string $method
     * @param array $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        return call_user_func_array(array($this->_memcache, $method), $params);
    }
}