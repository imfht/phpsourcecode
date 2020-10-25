<?php

namespace App\Http\Utils\Log;

class LogUtil {
	// 请求token
	public static $requestToken;
	
	/**
	 * 获取请求token
	 */
	public static function getRequestToken() {
		if (! self::$requestToken) {
			self::$requestToken = self::getServerAddr () . '-' . uniqid ();
		}
		
		return self::$requestToken;
	}
	
	/**
	 * 重置请求token
	 */
	public static function resetRequestToken() {
		self::$requestToken = self::getServerAddr () . '-' . uniqid ();
	}
	
	/**
	 * 获取请求地址
	 *
	 * @return string
	 */
	public static function getServerAddr() {
		return isset ( $_SERVER ['SERVER_ADDR'] ) ? substr ( $_SERVER ['SERVER_ADDR'], - 3 ) : '';
	}
}