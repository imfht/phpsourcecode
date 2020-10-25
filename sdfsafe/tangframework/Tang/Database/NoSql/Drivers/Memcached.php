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
use Memcache;
use Tang\Database\NoSql\Interfaces\INoSql;
use Tang\Exception\SystemException;

/**
 * Memcached类
 * 需要Memcache扩展
 * Class Memcached
 * @package Tang\Database\NoSql\Drivers
 */
class Memcached implements INoSql
{
    /**
     * 配置
     * @var array
     */
    protected $config = array();
	/**
	 * @var Memcache
	 */
	protected $memcache;

    /**
     * 设置配置
     * @param array $config
     */
    public function setConfig(array $config)
	{
		$this->config = $config;
		$this->connect();
	}

    /**
     * 连接服务器
     * @return mixed|void
     * @throws \Tang\Exception\SystemException
     */
    public function connect()
	{
		$memcache = new Memcache();
		foreach ($this->config['servers'] as $server)
		{
			$server = array_replace_recursive(array('host'=>'127.0.0.1','port'=>11211,'weight'=>1),$server);
			$memcache->addServer($server['host'],$server['port'],true,!$server['weight'] ? 1:$server['weight']);
		}
		if($memcache->getVersion() === false)
		{
			throw new SystemException('Could not establish Memcached connection.');
		}
		$this->memcache = $memcache;
	}

	/**
	 * 返回Memcached
	 * @return Memcache
	 */
	public function getMemcache()
	{
		return $this->memcache;
	}

	/**
	 * 关闭
	 * @return mixed
	 */
	public function close()
	{
		if($this->memcache)
			$this->memcache->close();
	}

	public function __call($method,$parameters)
	{
		return call_user_func_array(array($this->memcache,$method),$parameters);
	}
}