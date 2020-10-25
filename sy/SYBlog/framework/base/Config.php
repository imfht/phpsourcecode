<?php

/**
 * 设置基本类
 * 现已支持Yaconf、QConf
 * Yaconf文档：http://www.laruence.com/2015/06/12/3051.html
 * QConf文档：https://github.com/Qihoo360/QConf/wiki/QConf-PHP-Doc
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Base
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=frameworkr&type=license
 */

namespace sy\base;
use \Sy;
use \Yaconf;
use \sy\base\SYException;

final class Config {
	const YACONF = 1;
	const QCONF = 2;
	const FILE = 3;
	protected $appName;
	protected $type;
	protected $conf;
	//替换掉已有的配置，最高优先级
	protected $replaceConf = [];
	public function __construct($conf, $appName = NULL) {
		$this->appName = $appName;
		if (is_array($conf)) {
			$this->type = self::FILE;
			$this->conf = $conf;
		} elseif ($conf === self::YACONF) {
			$this->type = self::YACONF;
		} elseif ($conf === self::QCONF) {
			$this->type = self::QCONF;
		} elseif (is_file($conf)) {
			$this->type = self::FILE;
			$this->conf = require($conf);
		} else {
			throw new SYException();
		}
	}
	/**
	 * 获取配置
	 * @access public
	 * @param string $key 形似aaa.bbb.ccc的key，会根据不同类型自动处理
	 */
	public function get($key) {
		if (isset($this->replaceConf[$key])) {
			return $this->replaceConf[$key];
		}
		switch ($this->type) {
			case self::YACONF:
				return $this->getByYaconf($key);
				break;
			case self::QCONF:
				return $this->getByQconf($key);
				break;
			case self::FILE:
				return $this->getByConf($key);
				break;
		}
		return NULL;
	}
	public function getByYaconf($key) {
		if (!empty($this->appName)) {
			$key = $this->appName . '.' . $key;
		}
		return Yaconf::has($key) ? Yaconf::get($key) : NULL;
	}
	public function getByQconf($key) {
		$key = str_replace('.', '/', $key);
		if (!empty($this->appName)) {
			$key = '/' . $this->appName . '/' . $key;
		}
		return getConf($key);
	}
	public function getByConf($key) {
		$key = explode('.', $key);
		$conf = $this->conf;
		foreach ($key as $v) {
			if (isset($conf[$v])) {
				$conf = $conf[$v];
			} else {
				return NULL;
			}
		}
		return $conf;
	}
	/**
	 * 替换掉默认配置
	 * @access public
	 * @param string $key
	 * @param mixed $val
	 */
	public function replace($key, $val) {
		$this->replaceConf[$key] = $val;
	}
	public function has($key) {
		return $this->get($key) !== NULL;
	}
	/**
	 * 魔术方法，方便调用
	 */
	public function __get($k) {
		return $this->get($k);
	}
	public function __set($k , $v) {
		$this->replace($k, $v);
	}
	public function __isset($k) {
		return $this->has($k);
	}
}