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

class ocache{
	static $ocache = array();
	
	/**
	 * @return mucache
	 */
	public static function cache(){
		/*
		if( ! is_object( self::$ocache['cache'])){
			include_once RC_PATH_LIB . 'class.Mucache.php';
			if( !preg_match("/windows/i",functions::getos())){
				self::$ocache['cache'] = new mucache( rc::$construct['memcache'], false);
			}else{
				$mem = new Memcache;$mem->connect('127.0.0.1', 11210);
				self::$ocache['cache'] = $mem;
			}
			
		}
		*/
		
		if( ! is_object( self::$ocache['cache'])){
			include_once RC_PATH_LIB . 'cache/class.Mucache.php';
			self::$ocache['cache'] = new Mucache( C('MEMCACHED'), false);
		}
		return self::$ocache['cache'];
	}
	
	/**
	 * @return mucache
	 */
	public static function cachedb(){
		if( ! is_object( self::$ocache['cachedb'])){
			include_once RC_PATH_LIB . 'cache/class.Mucache.php';
			self::$ocache['cachedb'] = new Mucache( C('MEMCACHEDB'), false);
		}
		return self::$ocache['cachedb'];
	}
	
	/**
	 * @return mucache
	 */
	public static function cacheq(){
		if( ! is_object( self::$ocache['cacheq'])){
			include_once RC_PATH_LIB . 'cache/class.Mucache.php';
			self::$ocache['cacheq'] = new Mucache( C('MEMCACHEQ'), false);
		}
		return self::$ocache['cacheq'];
	}
	
	/**
	 * @return Tyrant
	 */
	public static function Tyrant(){
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
