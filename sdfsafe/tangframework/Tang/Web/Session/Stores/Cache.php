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
namespace Tang\Web\Session\Stores;
use SessionHandlerInterface;
use Tang\Cache\Stores\Store;

/**
 * 使用缓存构建session
 * Class Cache
 * @package Tang\Web\Session\Stores
 */
class Cache implements SessionHandlerInterface
{
	/**
	 * 缓存对象
	 * @var Store
	 */
	protected $cache;
	/**
	 * session生存周期
	 * @var int
	 */
	protected $expire;
	public function __construct(Store $cache,$expire)
	{
		$this->cache = clone $cache;
		$this->expire = $expire;
	}
	public function open($savePath,$sessionName)
	{
		return true;
	}
	public function read($sessionId)
	{
		return $this->cache->get($sessionId);
	}
	public function write($sessionId,$data)
	{
		$this->cache->set($sessionId,$data,$this->expire);
        return true;
	}
	public function destroy($sessionId)
	{
		$this->cache->delete($sessionId);
        return true;
	}
	public function gc($lifetime)
	{
		return true;
	}
	public function close()
	{
		return true;
	}
}