<?php
/**
 * Yaf设置支持类
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
use Yesf\Exception\RequirementException;
use Yesf\Config\ConfigTrait;
use Yesf\Config\ConfigInterface;

class Yaf implements ConfigInterface {
	use ConfigTrait;
	/** @var string $environment Environment name */
	protected $environment;

	/** @var array $conf Configs */
	protected $conf;

	/**
	 * Constructor
	 * 
	 * @access public
	 * @param string $file
	 */
	public function __construct($file) {
		if (!extension_loaded('Yaf')) {
			throw new RequirementException("Extension Yaf is required");
		}
		$this->environment = Yesf::app()->getEnvironment();
		if (class_exists('\\Yaf_Config_Ini', false)) {
			$this->conf = new \Yaf_Config_Ini($conf, $this->environment);
		} else {
			$this->conf = new \Yaf\Config\Ini($conf, $this->environment);
		}
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
		if (!$this->has($key)) {
			return $default;
		}
		$rs = $this->conf->get($key);
		if (is_object($rs)) {
			$rs = $rs->toArray();
		}
		return $rs;
	}
	/**
	 * 检查配置是否存在
	 * 
	 * @access public
	 * @param string $key 形似a.b.c的key
	 * @return bool
	 */
	public function has($key) {
		return isset($this->conf[$key]);
	}
}