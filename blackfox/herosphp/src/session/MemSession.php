<?php
/**
 * session的memcache实现
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since v1.2.1
 */
namespace herosphp\session;

use herosphp\core\Loader;
use herosphp\session\interfaces\ISession;

class MemSession implements  ISession {

    /**
     * handler for memcache server
     * @var Object
     */
    private static $handler = NULL;

    /**
     * @var array 配置信息
     */
    private static $config;

	/**
	 * @see	\herosphp\session\interfaces\ISession::start().
	 */
	public static function start($config = NULL) {

        self::$handler = new \Memcache();
        self::$handler->connect($config['host'], $config['port']) or E("could not to connect the memcache server!");
        self::$config = $config;
        if ( !$config['gc_maxlifetime'] ) {
            self::$config['gc_maxlifetime'] = ini_get('session.gc_maxlifetime');
        }

		session_set_save_handler(
			array(__CLASS__,'open'),
			array(__CLASS__,'close'),
			array(__CLASS__,'read'),
			array(__CLASS__,'write'),
			array(__CLASS__,'destroy'),
			array(__CLASS__,'gc')
		);
		session_start();
	}

	/**
	 * @see	\herosphp\session\interfaces\ISession::open().
	 */
	public static function open($savePath, $sessionName) {
		//do nothing here.
		return TRUE;
	}

	/**
	 * @see	\herosphp\session\interfaces\ISession::close().
	 */
	public static function close() {
		//do nothing here
		return TRUE;
	}

	/**
	 * @see	\herosphp\session\interfaces\ISession::read().
	 */
	public static function read($sessionId) {

		if ( self::$handler  == NULL ) return '';
		$data = self::$handler->get( $sessionId );
		return $data;

	}

	/**
	 * @see	\herosphp\session\interfaces\ISession::write().
	 */
	public static function write($sessionId, $data) {

		return self::$handler->set( $sessionId, $data,MEMCACHE_COMPRESSED, self::$config['gc_maxlifetime'] );
	}

	/**
	 * @see	\herosphp\session\interfaces\ISession::destroy().
	 */
	public static function destroy($sessionId) {
		$_SESSION = null;
		return self::$handler->delete( $sessionId );
	}

	/**
	 * @see	\herosphp\session\interfaces\ISession::gc().
	 */
	public static function gc($maxLifeTime) {
		//do nothing here.
		return TRUE;
	}

}
