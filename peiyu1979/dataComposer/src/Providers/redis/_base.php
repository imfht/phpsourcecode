<?php
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-2-9
 * Time: 11:45
 */

namespace DataComposer\Providers\redis;

use Log;

abstract class _base
{
	protected $redis;

	protected function init($host,$port,$database){
		$this->redis = new \Redis();
		$this->redis->connect($host,$port);
		$this->redis->select($database);
	}

	public function getDB(){
		return $this->redis;
	}
	public function setDB($redis){
		$this->redis=$redis;
	}


	public function get(array $keys){
		$_data=[];
		foreach ($keys as $key){

			switch ($this->redis->type($key)){
				case \Redis::REDIS_STRING://string
					$_data[]= $this->redis->get($key);
					break;
				case \Redis::REDIS_SET://set
					$_data[]= $this->redis->smembers($key);
					break;
				case \Redis::REDIS_LIST://list
					$_data[]= $this->redis->lrange($key,0,-1);
					break;
				case \Redis::REDIS_ZSET://zset
					$_data[]= $this->redis->zrevrange($key,0,-1);
					break;
				case \Redis::REDIS_HASH://hash
					$_data[]= $this->redis->hGetAll($key);
					break;
				case \Redis::REDIS_NOT_FOUND://other
					$_data[]=false;
					break;
			}

		}

		return $_data;
	}
}