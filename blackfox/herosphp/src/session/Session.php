<?php
/**
 * session工厂
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since v1.2.1
 */
namespace herosphp\session;

use herosphp\cache\RedisCache;
use herosphp\core\Loader;

class Session {

    /**
     * 开启session
     */
    public static function start() {

        //如果已经开启了SESSION则直接返回
        if ( isset($_SESSION) ) return true;

        //loading session configures
        $configs = Loader::config('session');
        $session_configs = $configs[$configs['session_handler']];
        switch ( $configs['session_handler'] ) {

            case 'file':
                FileSession::start($session_configs);
                break;

            case 'memo':
                MemSession::start($session_configs);
                break;

            case 'redis':
                RedisSession::start($session_configs);
                break;
        }

    }

    /**
     * 强制进行GC
     */
    public static function gc() {

        //loading session configures
        $configs = Loader::config('session');
        $session_configs = $configs[$configs['session_handler']];
        switch ( $configs['session_handler'] ) {

            case 'file':
                FileSession::gc($session_configs);
                break;

            case 'memo':
                MemSession::gc();
                break;

            case 'redis':
                RedisSession::gc();
                break;
        }
        $_SESSION = null;

    }

    /**
     * 获取session值
     * @param $key
     * @return mixed
     */
    public static function get($key) {
        return $_SESSION[$key];
    }

    /**
     * 设置session值
     * @param $key
     * @param $value
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
	
}