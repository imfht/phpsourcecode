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
 * Mysql连接器
 * Class Mysql
 * @package Tang\Database\Sql\Connectors
 */
class Mysql extends Connector
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
		if (isset($config['unixSocket']))
		{
			$pdo->exec('use '.$this->config['dbName']);
		}
		if(isset($this->config['charset']) && $this->config['charset'])
		{
			$collate = isset($this->config['collate']) && $this->config['collate'] ? ' collate \''.$this->config['collate'].'\'':'';
			$pdo->prepare('set names \''.$this->config['charset'].'\''.$collate)->execute();
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
		return isset($config['unixSocket']) && $config['unixSocket'] ? $this->getSocketDsn($config) : $this->getHostDsn($config);
	}

    /**
     * 获取SOCKET DSN
     * @param array $config
     * @return string
     */
    protected function getSocketDsn(array $config)
	{
		return "mysql:unix_socket={$config['unixSocket']};dbname=".$this->config['dbName'];
	}

    /**
     * 获取HOST DSN
     * @param array $config
     * @return string
     */
    protected function getHostDsn(array $config)
	{
		$port = isset($config['port']) && $config['port'] ? 'port='.$config['port'].';':'';
		return 'mysql:host='.$config['host'].';'.$port.'dbname='.$this->config['dbName'];
	}
}