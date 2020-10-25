<?php
/**
 * QConf设置支持类
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Config\Adapter;

use Yesf\Yesf;
use Yesf\Config\ConfigTrait;
use Yesf\Config\ConfigInterface;

class QConf implements ConfigInterface {
	use ConfigTrait;
	protected $appName;
	protected $environment;
	public function __construct($appName = null) {
		$this->appName = $appName;
		$this->environment = Yesf::app()->getEnvironment();
	}
	/**
	 * Get real key
	 * 
	 * @access protected
	 * @param string $key original key
	 * @return string
	 */
	protected function getKey($key) {
		$key = '/' . $this->environment . '.' . $key;
		$key = str_replace('.', '/', $key);
		if (!empty($this->appName)) {
			$key = '/' . $this->appName . $key;
		}
		return $key;
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
		$key = $this->getKey($key);
		$result = getConf($key);
		return $result === null ? $default : $result;
	}
	/**
	 * 检查配置是否存在
	 * 
	 * @access public
	 * @param string $key 形似a.b.c的key
	 * @return bool
	 */
	public function has($key) {
		return $this->get($key) !== null;
	}
}