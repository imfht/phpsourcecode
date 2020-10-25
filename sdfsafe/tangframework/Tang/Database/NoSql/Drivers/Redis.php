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
namespace Tang\Database\NoSql\Drivers;
use Redis as RedisClient;
use Tang\Database\NoSql\Interfaces\IRedis;
use Tang\Exception\SystemException;

/**
 * Redis
 * Class Redis
 * @package Tang\Database\NoSql\Drivers
 */
class Redis implements IRedis
{
	/**
     * 配置文件
	 * @var array
	 */
	protected $config = array();
	/**
     * 写Redis
	 * @var RedisClient
	 */
	protected $writeRedis;
	/**
     * 读Redis
	 * @var RedisClient
	 */
	protected $readRedis;
    /**
     * Redis选项
     * @var array
     */
    protected $options = array();

    /**
     * 设置配置
     * @param array $config
     */
    public function setConfig(array $config)
	{
		$config = array_replace_recursive(array('isMasterSlave'=>false,'isPconnect'=>true,'password'=>'','timeout'=>0,'prefix'=>'','serialize'=>2),$config);
		$this->config = $config;
		$this->options[RedisClient::OPT_PREFIX] = $config['prefix'];
		$this->options[RedisClient::OPT_SERIALIZER] = $config['serialize'];
		$this->connect();
	}

    /**
     * 链接
     * @return mixed|void
     * @throws \Tang\Exception\SystemException
     */
    public function connect()
	{
		$servers = count($this->config['servers']);
		if($servers == 0)
		{
			throw new SystemException('The Redis server configuration information is empty!');
		}
		try
		{
			$this->writeRedis = $this->createRedis(0);
		} catch(\RedisException $e)
		{
			throw new SystemException('The configuration index for the [%d] Redis link failure!error:%s',array(0,$e->getMessage()));
		}
		if($this->config['isMasterSlave'] && $servers > 1)
		{
			try
			{
				$this->readRedis = $this->createRedis(mt_rand(1,$servers-1));
			} catch(\RedisException $e)
			{
				$this->readRedis = $this->writeRedis;
				//log 记录
			}
		} else
		{
			$this->readRedis = $this->writeRedis;
		}
	}

    /**
     * 获取写Redis
     * @return \Redis
     */
    public function getWriteRedis()
	{
		return $this->writeRedis;
	}

    /**
     * 获取读Redis
     * @return \Redis
     */
    public function getReadRedis()
	{
		return $this->readRedis;
	}

    /**
     * 关闭
     * @return mixed|void
     */
    public function close()
	{
		if($this->writeRedis != $this->readRedis)
		{
			$this->readRedis->close();
		}
		$this->writeRedis->close();
	}

    /**
     * 折构函数
     */
    public function __destruct()
	{
		if(!$this->config['isPconnect'])
		{
			$this->close();
		}
	}

    /**
     * 根据索引创建Redis
     * @param $index
     * @return \Redis
     */
    protected function createRedis($index)
	{
		$config = $this->config['servers'][$index];
		$config = array_replace_recursive(array('host'=>'127.0.0.1','port'=>'6379','password'=>''),$config);
		$redis = new RedisClient();
		$method = $this->config['isPconnect'] ? 'pconnect':'connect';
		if(isset($config['unixSocket']) && $config['unixSocket'])
		{
			$redis->{$method}($config['unixSocket']);
		} else
		{
			$redis->{$method}($config['host'],$config['port'],$this->config['timeout']);
		}
		if($config['password'])
		{
			$redis->auth($config['password']);
		} else if($this->config['password'])
		{
			$redis->auth($this->config['password']);
		}
		foreach($this->options as $key=>$value)
		{
			$redis->setOption($key,$value);
		}
		if(isset($config['index']) && ($config['index'] = (int)$config['index']) > 0 && $config['index'] < 16)
		{
			$redis->select($config['index']);
		}
		return $redis;
	}
}