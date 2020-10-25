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
namespace Tang\Database\Sql;
use Tang\Database\Sql\Connections\Connection;
use Tang\Services\ConfigService;
use Tang\Exception\SystemException;
use Tang\Database\Sql\Connectors\IConnector;

/**
 * 数据库获取链接对象
 * Class DB
 * @package Tang\Database\Sql
 */
class DB
{
    /**
     * 链接对象
     * @var array
     */
    private static $connections = array();
	/**
	 * 获取数据库实例
	 * @param string $name 数据库标示
	 * @return Connection
	 */
	public static function get($name = '')
	{
		if(!$name)
		{
			$name = static::getDefautlName();
		}
		if(!isset(static::$connections[$name]))
		{
			static::$connections[$name] = static::create($name);
		}
		return static::$connections[$name];
	}
	private static function getDefautlName()
	{
		$names = array_keys(ConfigService::getService()->get('database.*'));
		return $names[0];
	}

    /**
     * 创建数据库实例
     * @param $name
     * @return mixed
     * @throws \Tang\Exception\SystemException
     */
    private static function create($name)
	{
		$dbConfig = ConfigService::getService()->get('database.'.$name);
		if(!$dbConfig)
		{
			throw new SystemException('Does not contain "database configuration information for the name"',array($name),40000);
		}
		$dbConfig['driver'] = strtolower($dbConfig['driver']);
		$driverClass = '';
		switch ($dbConfig['driver'])
		{
			case 'mysql':
				$driverClass = 'Mysql';
				break;
			case 'pgsql':
				$driverClass = 'Pgsql';
				break;
			case 'mssql':
				$driverClass = 'Mssql';
				break;
			case 'sqllite':
				$driverClass = 'SqlLite';
				break;
			default:
				throw new SystemException('[%s] does not support the type of database',array($dbConfig['driver']),40001);
		}
		$connector = $connection = null;
		//如果包含自己的数据库驱动类
		if(isset($dbConfig['class']) && $dbConfig['class'])
		{
			$connector = new $dbConfig['class']();
			if(!$connector instanceof IConnector)
			{
				throw new SystemException('The [%s] class does not implement the [%s] interface',array($dbConfig['class'],'\Tang\Database\Sql\Connectors\IConnector'),20002);
			}
		} else 
		{
			$connectorClass = '\Tang\Database\Sql\Connectors\\'.$driverClass;
			$connector = new $connectorClass();
		}
		$connector->setConfig($dbConfig);
		$connector->connect();
		$connectorClass = '\Tang\Database\Sql\Connections\\'.$driverClass;
		return new $connectorClass($connector->getWritePdo(),$connector->getReadPdo(),$dbConfig['dbName'],$dbConfig['tablePrefix'],$dbConfig);
	}
}