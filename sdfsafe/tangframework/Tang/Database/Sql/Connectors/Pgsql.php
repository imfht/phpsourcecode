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
namespace Tang\Database\Sql\Connectors;
/**
 * Pgsql连接构造
 * Class Pgsql
 * @package Tang\Database\Sql\Connectors
 */
class Pgsql extends  Connector
{
    /**
     * 连接处理
     * @param $config
     * @return \PDO
     */
	protected function connectHandler($config)
	{
		$dsn = $this->getDsn($config);
		$pdo = $this->createPDO($dsn, $config);
		if($this->config['charset'])
		{
			$pdo->prepare('set names \''.$this->config['charset'].'\'')->execute();
		}
		if (isset($config['schema']) && $config['schema'])
		{
			$pdo->prepare('set search_path to '.$config['schema'])->execute();
		}
		return $pdo;
	}
    /**
     * 获取DSN
     * @param array $config
     * @return string
     */
	protected function getDsn(array $config)
	{
		$host = isset($config['host']) ? 'host='.$config['host'].';' : '';
	
		$dsn = 'pgsql:'.$host.'dbname='.$this->config['dbName'];
		if (isset($config['port']) && $config['port'])
		{
			$dsn .= ';port='.$config['port'];
		}
		return $dsn;
	}
}