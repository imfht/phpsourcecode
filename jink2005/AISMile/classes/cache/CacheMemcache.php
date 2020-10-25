<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/**
 * This class require PECL Memcache extension
 *
 */
class CacheMemcacheCore extends Cache
{
	/**
	 * @var Memcache
	 */
	protected $memcache;

	/**
	 * @var bool Connection status
	 */
	protected $is_connected = false;

	public function __construct()
	{
		$this->connect();

		// Get keys (this code comes from Doctrine 2 project)
        $this->keys = array();
        $all_slabs = $this->memcache->getExtendedStats('slabs');

        foreach ($all_slabs as $server => $slabs)
        {
            if (is_array($slabs))
            {
                foreach (array_keys($slabs) as $slab_id)
                {
                    $dump = $this->memcache->getExtendedStats('cachedump', (int)$slab_id);
                    if ($dump)
                    {
                       foreach ($dump as $entries)
                       {
                            if ($entries)
                                $this->keys = array_merge($this->keys, array_keys($entries));
                       }
                    }
                }
            }
        }
	}

	public function __destruct()
	{
		$this->close();
	}

	/**
	 * Connect to memcache server
	 */
	public function connect()
	{
		$this->memcache = new Memcache();
		$servers = CacheMemcache::getMemcachedServers();
		if (!$servers)
			return false;
		foreach ($servers as $server)
			$this->memcache->addServer($server['ip'], $server['port'], $server['weight']);

		$this->is_connected = true;
	}

	/**
	 * @see Cache::_set()
	 */
	protected function _set($key, $value, $ttl = 0)
	{
		if (!$this->is_connected)
			return false;
		return $this->memcache->set($key, $value, 0, $ttl);
	}

	/**
	 * @see Cache::_get()
	 */
	protected function _get($key)
	{
		if (!$this->is_connected)
			return false;
		return $this->memcache->get($key);
	}

	/**
	 * @see Cache::_exists()
	 */
	protected function _exists($key)
	{
		if (!$this->is_connected)
			return false;
		return isset($this->keys[$key]);
	}

	/**
	 * @see Cache::_delete()
	 */
	protected function _delete($key)
	{
		if (!$this->is_connected)
			return false;
		return $this->memcache->delete($key);
	}

	/**
	 * @see Cache::_writeKeys()
	 */
	protected function _writeKeys()
	{
	}

	/**
	 * @see Cache::flush()
	 */
	public function flush()
	{
		if (!$this->is_connected)
			return false;
		return $this->memcache->flush();
	}

	/**
	 * Close connection to memcache server
	 *
	 * @return bool
	 */
	protected function close()
	{
		if (!$this->is_connected)
			return false;
		return $this->memcache->close();
	}

	/**
	 * Add a memcache server
	 *
	 * @param string $ip
	 * @param int $port
	 * @param int $weight
	 */
	public static function addServer($ip, $port, $weight)
	{
		return Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'memcached_servers (ip, port, weight) VALUES(\''.pSQL($ip).'\', '.(int)$port.', '.(int)$weight.')', false);
	}

	/**
	 * Get list of memcached servers
	 *
	 * @return array
	 */
	public static function getMemcachedServers()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'memcached_servers', true, false);
	}

	/**
	 * Delete a memcache server
	 *
	 * @param int $id_server
	 */
	public static function deleteServer($id_server)
	{
		return Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'memcached_servers WHERE id_memcached_server='.(int)$id_server);
	}
}
