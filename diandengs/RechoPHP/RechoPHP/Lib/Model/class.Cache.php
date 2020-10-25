<?php
// +----------------------------------------------------------------------
// | RechoPHP [ WE CAN DO IT JUST Better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://recho.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: recho <diandengs@gmail.com>
// +----------------------------------------------------------------------

class Cache{
	static $ocache = array();
	
	private $options = array();
	
	public function __construct(){
		
	}
	
	static public function run( $mode=false){
		if( empty($mode)) $mode = C('DATA_CACHE_DEFAULT');
		if( !in_array( $mode, array('File', 'Memcache', 'Memcached'))) $mode = 'File';
		switch( $mode){
			case 'File':return self::fileCache();break;
			case 'Memcache':return self::memcache();break;
			case 'Memcached':return self::memcached();break;
		}
	}
	
	/**
	 * @return fileCache
	 */
	public static function fileCache(){
		if( ! is_object( self::$ocache['FileCache'])){
			include_once RC_PATH_LIB . 'cache/class.FileCache.php';
			$options = array( 'temp'=>C('DATA_CACHE_PATH'), 'expire'=>C('DATA_CACHE_TIME'));
			self::$ocache['FileCache'] = new FileCache( $options);
		}
		return self::$ocache['FileCache'];
	}
	
	/**
	 * @return memcache
	 */
	public static function memcache(){
		if( !class_exists( Memcache)) exit('the memcache lib is not int the system!');
		if( ! is_object( self::$ocache['cache'])){
			$config = C('MEMCACHE');
			$mem = new Memcache;$mem->connect($config['host'], $config['port']);
			self::$ocache['cache'] = $mem;
		}
		return self::$ocache['cache'];
	}
	
	/**
	 * @return memcached
	 */
	public static function memcached(){
		if( ! is_object( self::$ocache['cache'])){
			include_once RC_PATH_LIB . 'cache/class.Mucache.php';
			self::$ocache['cache'] = new Mucache( C('MEMCACHED'), false);
		}
		return self::$ocache['cache'];
	}
	
	/**
	 * @return Tyrant
	 */
	public static function tyrant(){
		if( ! is_object( self::$ocache['Tyrant'])){
			include_once RC_PATH_LIB . 'cache/class.Tyrant.php';
			self::$ocache['Tyrant'] = new Tyrant( C('TOKYO'), false);
		}
		return self::$ocache['Tyrant'];
	}
	
	static function destroy(){
		self::$ocache = array();
	}
}