<?php
/**
 * Session Handler
 *
 * @author ShuangYa
 * @package Yesf
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Http;

use SessionHandlerInterface;
use Yesf\Yesf;
use Psr\SimpleCache\CacheInterface;

class SessionHandler implements SessionHandlerInterface {
	/** @var CacheInterface $cache Cache Handler */
	private $cache;

	/** @var int $lifetime Session lifetime */
	private $lifetime;

	public function __construct(CacheInterface $cache) {
		$this->cache = $cache;
		$this->lifetime = Yesf::app()->getConfig('session.lifetime', Yesf::CONF_ENV, 720);
	}
	
	public function open($save_path, $session_name) {
		return true;
	}

	public function close() {
		return true;
	}

	public function destroy($session_id) {
		$this->cache->delete('sess_' . $session_id);
		return true;
	}

	public function gc($maxlifetime) {
		return true;
	}

	public function read($session_id) {
		$res = $this->cache->get('sess_' . $session_id, '');
		$this->cache->set('sess_' . $session_id, $res, $this->lifetime);
		return $res;
	}

	public function write($session_id, $session_data) {
		return $this->cache->set('sess_' . $session_id, $session_data, $this->lifetime);
	}
}
