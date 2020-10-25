<?php
namespace Yurun\Util\Swoole;

use Swoole\Coroutine\Channel;

abstract class SphinxPool
{
	/**
	 * 客户端数量
	 *
	 * @var int
	 */
	private static $clients;

	/**
	 * 等待时长，单位毫秒
	 *
	 * @var integer
	 */
	private static $waitTimeout = 3000;

	/**
	 * 队列
	 * 
	 * @var \Swoole\Coroutine\Channel
	 */
	protected static $queue;

	/**
	 * 池子存储
	 * 
	 * @var SphinxClient[]
	 */
	protected static $clientInstances = [];

	/**
	 * 初始化
	 *
	 * @param integer $clients 连接数
	 * @param string $host 主机
	 * @param integer $port 端口
	 * @param callable $initCallback 初始化回调，可以为null
	 * @return void
	 */
	public static function init(int $clients, string $host, int $port, callable $initCallback = null)
	{
		static::$clients = $clients;
		static::$queue = new Channel($clients);
		static::$clientInstances = [];
		for($i = 0; $i < $clients; ++$i)
		{
			$client = new SphinxClient;
			$client->SetServer($host, $port);
			if(is_callable($initCallback))
			{
				$initCallback($client);
			}
			static::$clientInstances[spl_object_hash($client)] = $client;
			static::$queue->push($client);
		}
	}

	/**
	 * 获取客户端数量
	 *
	 * @return int
	 */
	public static function getClients()
	{
		return static::$clients;
	}

	/**
	 * 获取客户端
	 *
	 * @return SphinxClient
	 */
	public static function getClient()
	{
		// 等待其他协程使用完成后释放连接
		$client = static::$queue->pop(static::$waitTimeout / 1000);
		if(false === $client)
		{
			throw new \RuntimeException('SphinxPool getClient timeout');
		}
		return $client;
	}

	/**
	 * 释放客户端
	 *
	 * @param SphinxClient $client
	 * @return void
	 */
	public static function releaseClient(SphinxClient $client)
	{
		$hash = spl_object_hash($client);
		if(isset(static::$clientInstances[$hash]))
		{
			static::$queue->push($client);
		}
	}

	/**
	 * 使用回调来使用池子中的资源，无需手动释放
	 * 本方法返回值为回调的返回值
	 * 
	 * @param callable $callback
	 * @return mixed
	 */
	public static function use(callable $callback)
	{
		$client = static::getClient();
		$result = null;
		try{
			$result = $callback($client);
		}
		finally{
			static::releaseClient($client);
		}
		return $result;
	}
}