<?php
/**
 * 缓存类
 * cache.php
 */
namespace framework\cache;
use framework\core\Abnormal,
    Templi;
use framework\core\Singleton;

/**
 * Class Cache
 * @package framework\cache
 * @proxy AbstractCache
 */
class Cache
{
    use Singleton;

    /** @var  AbstractCache */
    private $_cacheHandle;
    /**
     * 缓存类工厂方法
     * @param string $cacheType
     * @return \framework\cache\AbstractCache
     * @throws Abnormal
     */
    protected function init($cacheType=null)
    {
        $instance =  null;
        $app = Templi::getApp();
        if(empty($cacheType)){
            $cacheType = strtolower($app->getConfig('cache_type'));
        }
        switch ($cacheType) {
            case 'memcache':
                $class_name = '\\framework\\libraries\\cache\\CacheMemcache';
                $config['expire'] = $app->getConfig('cache_timeout');
                $config['host'] = $app->getConfig('cache_memcache_host');
                $config['port'] = $app->getConfig('cache_memcache_port');
                break;
            case 'memcached':
                $class_name = '\\framework\\libraries\\cache\\CacheMemcached';
                $config['host'] = $app->getConfig('cache_memcache_host');
                $config['port'] = $app->getConfig('cache_memcache_port');
                break;
            case 'file':
            default:
                $class_name ='\\framework\\libraries\\cache\\CacheFile';
                $config['date_type'] =  $app->getConfig('cache_datatype');
                $config['expire'] =  $app->getConfig('cache_timeout');
        }
//        $app->load->import(TEMPLI_PATH.'libraries/cache/AbstractCache.class.php');
//        $app->load->import(TEMPLI_PATH.'libraries/cache/' . $class_name . '.class.php');
        $instance = new $class_name($config);
        if($instance instanceof AbstractCache){
            $this->_cacheHandle = $instance;
        }else{
            throw new Abnormal('不支持的缓存方式'.$class_name, 500);
        }
    }

    /**
     * @param $method
     * @param array $params
     * @return mixed
     */
    public function __call($method, $params=array())
    {
        return call_user_func_array(array($this->_cacheHandle, $method), $params);
    }
}