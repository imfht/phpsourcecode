<?php
/**
 * session 封装
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date  2013-4-20
 */
namespace framework\session;
use framework\core\Singleton;
use \Templi,
    framework\core\Abnormal;

class Session extends \ArrayObject
{
    use Singleton;

    /**
     * 初始化 session
     */
    protected function init()
    {
        if (!isset($_SESSION)) {
            $instance = null;
            $storageType =  Templi::getApp()->getConfig('session_storage');
            $func = 'session'.ucfirst($storageType);
            if (method_exists($this, $func)) {
                self::$func();
            } else {
                throw new Abnormal('session 存储方式'.$storageType.'不支持', 500);
            }
        }
    }
    /**
     * 获取 session 信息
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        if(isset($name)){
            return $_SESSION[$name];
        }else{
            return NULL;
        }
    }
    /**
     * 设置session值
     * @param string|array $name
     * @param mixed $val
     * @return void
     */
    public function set($name, $val=null) {
        if(!$name) return;
        if(is_array($name)){
            foreach($name as $key=>$value){
                $this->set($key, $value);
            }
        }else{
            $_SESSION[$name] = $val;
        }
    }
    /**
     * 设置session值
     * @param mixed $name
     * @return void
     */
    public function remove($name) {
        if(is_array($name)){
            foreach($name as $val){
                $this->remove($val);
            }
        }else{
            unset($_SESSION[$name]);
        }
    }
    /**
     * 获得 当前 session id
     * @param int $id
     * @return string
     */
    public function id($id = null) {
        return empty($id) ? session_id() : session_id($id);
    }
    /**
     * 获得 当前session 名
     * @param string $name
     * @return string
     */
    static function name($name=null){
        return empty($name) ? session_name() : session_name($name);
    }
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
    public function count()
    {
        return count($_SESSION);
    }
    public function toJson()
    {
        return json_encode($_SESSION);
    }
    public function __get($name)
    {
        return $this->get($name);
    }
    public function __set($name, $val)
    {
        $this->set($name, $val);
    }
    public function __unset($name)
    {
        $this->remove($name);
    }
    public function __isset($name)
    {
        return isset($_SESSION[$name]);
    }
    /**
     * 以文件形式存储session
     * @return mixed
     */
    private static function sessionFile()
    {
        $class_name ='\\framework\\libraries\\SessionDrivers\\SessionFile';
        return new $class_name( Templi::getApp()->getConfig('session_savepath'));
    }

    /**
     * session 存储到mysql数据库
     * @return mixed
     */
    private static function sessionMysql()
    {
        $class_name = '\\framework\\libraries\\SessionDrivers\\SessionMysql';
        $sessionModel =  Templi::getApp()->getConfig('session_model');
        $lifetime =  Templi::getApp()->getConfig('session_lifetime');
        return new $class_name($sessionModel, $lifetime);
    }

    /**
     * session 存储到memcached
     */
    private static function sessionMemcached()
    {
        $class_name = '\\framework\\libraries\\SessionDrivers\\SessionMemcached';
        $memcaheHost =  Templi::getApp()->getConfig('session_memcache_host');
        $memcachePort =  Templi::getApp()->getConfig('session_memcache_port');
        if (class_exists('Memcached')) {
            $memcache = new \Memcached();
        } else {
            throw new Abnormal('未安装memcached扩展', 500);
        }
        $memcache->addServer($memcaheHost, $memcachePort);
        require_once('session/' . $class_name. '.class.php');
        return new $class_name($memcache);
    }

    /**
     * session 存储到memcache
     *
     * @throws Abnormal
     * @return mixed
     */
    private static function sessionMemcache()
    {
        $class_name = '\\framework\\libraries\\SessionDrivers\\SessionMemcache';
        $memcaheHost =  Templi::getApp()->getConfig('session_memcache_host');
        $memcachePort =  Templi::getApp()->getConfig('session_memcache_port');
        if(class_exists('Memcache')) {
            $memcache = new \Memcache();
        } else {
            throw new Abnormal('未安装memcached扩展', 500);
        }
        $memcache->connect($memcaheHost, $memcachePort);
        return new $class_name($memcache);
    }
}