<?php

/**
 * Created by PhpStorm.
 * Author: William
 * Date: 2016/9/13
 * Time: 0:36
 */
class Factory
{
	private static $loader;

	public static function getLoader()
	{
		return self::$loader;
	}

	public static function setLoader(Loader $Loader){
		self::$loader = $Loader;
	}
}