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
use \PDO;

/**
 * 数据库连接器基类
 * Class Connector
 * @package Tang\Database\Sql\Connectors
 */
abstract class Connector
{
    /**
     * 连接选项
     * @var array
     */
    protected $options = array(
			PDO::ATTR_PERSISTENT => true,
			PDO::ATTR_CASE => PDO::CASE_NATURAL,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
			PDO::ATTR_TIMEOUT => 100,
			PDO::ATTR_STRINGIFY_FETCHES => false);
    /**
     * 读PDO
     * @var PDO
     */
    protected $readPdo;
    /**
     * 写PDO
     * @var PDO
     */
    protected $writePdo;
    /**
     * 配置
     * @var array
     */
    protected $config;

    /**
     * 获取写PDO
     * @return PDO
     */
    public function getWritePdo()
	{
		return $this->writePdo;
	}

    /**
     * 获取读PDO
     * @return PDO
     */
    public function getReadPdo()
	{
		return $this->readPdo;
	}

    /**
     * 设置配置文件
     * @param array $config
     */
    public function setConfig(array $config)
	{
		$this->config = $config;
		$this->options[PDO::ATTR_PERSISTENT] = $config['persistent'] == 1;
	}

    /**
     * 连接
     */
    public function connect()
	{
		$readWriteSeparate = $this->config['readWriteSeparate'];
		if($readWriteSeparate['support'] == 1)
		{
			$this->multiConnect((int)$readWriteSeparate['masterNumber']);
		} else 
		{
			$this->writePdo = $this->readPdo = $this->connectHandler($this->config['info'][0]);
		}
	}

    /**
     * 主从连接
     * @param $masterNumber
     */
    protected function multiConnect($masterNumber)
	{
		$readIndex = $writeIndex = 0;
		$dbCount = count($this->config['info']) -1;
		if($masterNumber > 0)
		{
			//选出一个主
			$writeIndex = floor(mt_rand(0, $masterNumber-1));
			$readIndex = floor(mt_rand($masterNumber, $dbCount));
		} else //默认只为一个主
		{
			$readIndex = floor(mt_rand(1, $dbCount));
		}
		if($writeIndex > $dbCount)
		{
			$writeIndex = 0;
		}
		if($readIndex > $dbCount)
		{
			$readIndex = 0;
		}
		$this->writePdo = $this->connectHandler($this->config['info'][$writeIndex]);
		if($writeIndex == $readIndex)
		{
			$this->readPdo = $this->writePdo;
		} else 
		{
			$this->readPdo = $this->connectHandler($this->config['info'][$readIndex]);
		}
	}

    /**
     * 创建PDO
     * @param $dsn
     * @param array $config
     * @param array $options
     * @return PDO
     */
    protected function createPDO($dsn,array $config,array $options = array())
	{
		$username = isset($config['username']) && $config['username']? $config['username']:'';
		$password = isset($config['password']) && $config['password']? $config['password']:'';
		if($options)
		{
			$options = array_replace_recursive($this->options, $options);
		} else 
		{
			$option = $this->options;
		}
		return new PDO($dsn,$username, $password,$option );
	}

    /**
     * 数据库里连接处理程序
     * @param $config
     * @return mixed
     */
    protected abstract function connectHandler($config);
}