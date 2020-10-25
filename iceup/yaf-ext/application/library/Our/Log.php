<?php

namespace Our;

/**
 * 日志类
 */
class Log {

    private $_handle = null;

    /**
     * 写入日志
     * 
     * @param string $message
     */
    public function write($message) {
        fwrite($this->getHandle(), date("Y-m-d H:i:s")
                . "\t" . $message
                . "\turi:" . $_SERVER['REQUEST_URI']
                . "\tref:" . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '')
                . "\r\n");
    }

    /**
     * 获取打开文件句柄
     * 
     * @return 
     */
    public function getHandle() {
        if (!$this->_handle) {
            $this->_handle = fopen(APPLICATION_PATH . '/data/log/application.log', 'a');
        }

        return $this->_handle;
    }

    /**
     * 获取实例
     * 
     * @return \Our\Log
     */
    private static $_instance = null;

    /**
     * 获取实例
     * 
     * @return \Our\Log
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

}
