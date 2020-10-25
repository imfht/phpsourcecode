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
use Tang\Exception\SystemException;
use Tang\Services\ConfigService;

/**
 * Nosql快速获取类
 * Class Base
 * @package Tang\Database\NoSql
 */
abstract class Base
{
    /**
     * Nosql实例
     * @var array
     */
    protected static $instances = array();

    /**
     * 根据$name获取Nosql实例
     * @param string $name
     * @throws \Tang\Exception\SystemException
     */
    public static function get($name='')
	{
		$type = static::getType();
		$cacheName = $type.'_'.$name;
		if(isset(static::$instances[$cacheName]))
		{
			return static::$instances[$cacheName];
		}
		$config = ConfigService::getService()->get('noSql.'.$type);
		if(!isset($config['servers']) || !is_array($config['servers']) || !$config['servers'])
		{
			throw new SystemException('[%s] is not configured!',array($type));
		}
		$servers = array();
		if(!$name)
		{
			$servers = reset($config['servers']);
		} else if(!isset($config['servers'][$name]))
		{
			throw new SystemException('The [%s] driver is not found in the [%s] data source!',array($type,$name));
		} else
		{
			$servers = $config['servers'][$name];
		}
		$config['servers'] = $servers;
		$instance = static::createInstance();
		$instance->setConfig($config);
		static::$instances[$cacheName] = $instance;
		return $instance;
	}

    /**
     * 获取Nosql类型
     */
    public static function getType(){}

    /**
     * 创建Nosql实例
     */
    public static function createInstance(){}
}