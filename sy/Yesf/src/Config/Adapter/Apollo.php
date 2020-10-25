<?php
/**
 * Apollo设置支持类
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Config\Adapter;

use SplQueue;
use CIDRmatch\CIDRmatch;
use Swoole\Coroutine\Http\Client;
use Yesf\Yesf;
use Yesf\DI\Container;
use Yesf\Exception\Exception;
use Yesf\Config\ConfigTrait;
use Yesf\Config\ConfigInterface;

/**
 * Apllo adapter
 * 
 * Using: new Apllo([
 *  'host' => '127.0.0.1',
 *  'port' => 80,
 *  'path' => '',
 *  'appid' => '123456'
 *  'cluster' => 'default',
 *  'namespace' => '1,2,3',
 *  'with_ip' => false,
 *  'with_ip' => '192.168.0.0/24'
 * ])
 */
class Apollo implements ConfigInterface {
	/** @var array $conf Apollo Config */
	protected $conf;

	/** @var array $cache Config cache */
	protected $cache;

	/** @var int $last_fetch Last update time */
	protected $last_fetch = 0;

	/** @var array $last_key Last key returned by Apollo */
	protected $last_key = [];

	/**
	 * Constructor
	 * 
	 * @access public
	 * @param array/string $conf Apollo config
	 */
	public function __construct($conf) {
		$this->environment = Yesf::app()->getEnvironment();
		if (is_string($conf)) {
			$conf = Yesf::app()->getConfig($conf, Yesf::CONF_PROJECT);
		}
		$this->conf = $conf;
		if (!isset($this->conf['cluster'])) {
			$this->conf['cluster'] = 'default';
		}
		if (is_string($this->conf['namespace'])) {
			$this->conf['namespace'] = explode(',', $this->conf['namespace']);
		}
	}
	/**
	 * Get HTTP Client
	 * 
	 * @access protected
	 * @param string $namespace
	 * @return object
	 */
	protected function getClient($namespace) {
		$path = sprintf('%s/configs/%s/%s/%s?releaseKey=%s',
			$this->conf['path'],
			$this->conf['appid'],
			$this->conf['cluster'],
			$this->conf['namespace'],
			$this->last_key);
		if ($this->conf['with_ip']) {
			$cidr = Container::getInstance()->get(CIDRmatch::class);
			foreach (swoole_get_local_ip() as $v) {
				if ($cidr->match($v, $this->conf['with_ip'])) {
					$path .= '&ip=' . $v;
					break;
				}
			}
		}
		$cli = new Client($this->conf['host'], $this->conf['port']);
		$cli->set([
			'timeout' => 1
		]);
		$cli->setDefer();
		$cli->get($path);
		return $cli;
	}
	/**
	 * Update cache with result
	 * 
	 * @access protected
	 * @param string $namespace
	 * @param array $config
	 */
	protected function update($namespace, $config) {
		foreach ($config as $k => $v) {
			if (strpos($k, '.') === false) {
				$this->cache[$namespace][$k] = $v;
				continue;
			}
			$keys = explode('.', $k);
			$total = count($keys) - 1;
			$parent = &$this->cache[$namespace];
			foreach ($keys as $kk => $vv) {
				if ($total === $kk) {
					$parent[$vv] = $v;
				} else {
					if (!isset($parent[$vv])) {
						$parent[$vv] = [];
					}
					$parent = &$parent[$vv];
				}
			}
		}
	}
	/**
	 * Refresh all configs
	 * 
	 * @access public
	 * @param bool $force Is force refresh or not
	 */
	public function refresh($force = false) {
		if (!$force && time() - $this->last_fetch < $this->config['refresh_interval']) {
			return;
		}
		$clis = [];
		foreach ($this->conf['namespace'] as $v) {
			$clis[] = $this->getClient($v);
		}
		foreach ($clis as $cli) {
			$cli->recv();
			if ($cli->statusCode === 304) {
				continue;
			}
			$res = json_decode($cli->body, true);
			$ns = $res['namespaceName'];
			$this->last_key[$ns] = $res['releaseKey'];
			$this->update($ns, $res['configurations']);
		}
		$this->last_fetch = time();
	}
	/**
	 * 获取配置
	 * 
	 * @access public
	 * @param string $key 形似a.b.c的key
	 * @param mixed $default 默认
	 * @return mixed
	 */
	public function get($key, $default = null) {
		$this->refresh();
		$keys = explode('.', $key);
		$conf = $this->cache;
		foreach ($keys as $v) {
			if (isset($conf[$v])) {
				$conf = $conf[$v];
			} else {
				return $default;
			}
		}
		return $conf;
	}
	/**
	 * 检查配置是否存在
	 * 
	 * @access public
	 * @param string $key 形似a.b.c的key
	 * @return bool
	 */
	public function has($key) {
		$this->refresh();
		$keys = explode('.', $key);
		$conf = $this->cache;
		foreach ($keys as $v) {
			if (isset($conf[$v])) {
				$conf = $conf[$v];
			} else {
				return false;
			}
		}
		return true;
	}
}