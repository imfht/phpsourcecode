<?php
namespace Scabish\Tool;

/**
 * Scabish\Core\Session
 * Session操作类
 * 
 * @author keluo <keluo@focrs.com>
 * @copyright 2016 Focrs, Co.,Ltd
 * @package Scabish
 * @since 2015-01-24
 */
class Session {
    
    /**
     * @var string session标识
     */
    private $_sessionID;
    
    private $_data;
    
    public function __construct() {
        $this->Start();
        $this->_sessionID = session_id();
        if(isset($_SESSION[$this->_sessionID])) {
            $this->_data = unserialize($_SESSION[$this->_sessionID]);
        } else {
            $this->_data = array();
        }
    }
    
    public function __set($key, $value) {
        $this->_data[$key] = $value;
        $_SESSION[$this->_sessionID] = serialize($this->_data);
    }

    public function __get($key) {
        return isset($this->_data[$key]) ? $this->_data[$key] : false;
    }
    
    public function Start() {
        if(version_compare(phpversion(), '5.4.0', '>=')) {
            $status = session_status() === PHP_SESSION_ACTIVE;
        } else {
            $status = session_id() === '' ? false : true;
        }
        
        if(!$status) session_start();
    }
    
    public function Delete($key) {
        if(isset($this->_data[$key])) {
            unset($this->_data[$key]);
            $_SESSION[$this->_sessionID] = serialize($this->_data);
        }
    }
    
    public function Clear() {
        $this->_data = array();
        session_unset();
    }
}