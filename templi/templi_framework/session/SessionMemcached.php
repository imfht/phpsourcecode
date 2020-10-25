<?php

namespace framework\session;

/**
 *
 * Class SessionMemcached
 * @package framework\libraries\SessionDrivers
 */
class SessionMemcached
{
    /**
     * @var \Memcache memcache实例
     */
    private $_memcached = null;

    private $_lifetime = 0;
    /**
     * 构造函数
     *
     */
    public function __construct($memcached, $lifetime=null) {
        $this->_memcached = $memcached;
        if (!empty($lifetime)) {
            ini_set('session.gc_maxlifetime', $lifetime);
            $this->_lifetime = $lifetime;
        } else {
            $this->_lifetime = ini_get('session.gc_maxlifetime');
        }
        session_set_save_handler(
            array(&$this, 'open'), array(&$this, 'close'),
            array(&$this, 'read'), array(&$this, 'write'),
            array(&$this, 'destroy'), array(&$this, 'gc')
        );
        session_start();
    }
    /**
     * session_set_save_handler  open方法
     * @param string $savePath
     * @param string $sessionName
     * @return true
     */
    public function open($savePath, $sessionName) {
        return true;
    }
    /**
     * session_set_save_handler  close方法
     * @return bool
     */
    public function close() {
        return true;
    }
    /**
     * 读取session_id
     * session_set_save_handler  read方法
     * @param $sessionId sessionId
     * @return string 读取session_id
     */
    public function read($sessionId) {
        return $this->_memcached->get($sessionId);
    }
    /**
     * 写入session_id 的值
     *
     * @param $sessionId session
     * @param $data 值
     * @return mixed query 执行结果
     */
    public function write($sessionId, $data) {
        return $this->_memcached->set($sessionId, $data, $this->_lifetime);
    }
    /**
     * 删除指定的session_id
     *
     * @param $sessionId session id
     * @return bool
     */
    public function destroy($sessionId) {
        return $this->_memcached->delete($sessionId);
    }
    /**
     * 删除过期的 session
     *
     * @param $lifetime 存活期时间
     * @return bool
     */
    public function gc($lifetime) {
        return true;
    }
}
