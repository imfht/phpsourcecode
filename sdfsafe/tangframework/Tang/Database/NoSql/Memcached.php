<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Database\NoSql;
use Tang\Database\NoSql\Drivers\Memcached as MemcachedDriver;

/**
 * Class Memcached
 * @package Tang\Database\NoSql
 */
class Memcached extends Base
{
	/**
	 * @param string $name
	 * @return MemcachedDriver
	 */
	public static function get($name='')
	{
		return parent::get($name);
	}
	public static function getType()
	{
		return 'memcached';
	}
	public static function createInstance()
	{
		return new MemcachedDriver();
	}
}