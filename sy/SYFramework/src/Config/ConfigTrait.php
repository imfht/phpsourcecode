<?php
/**
 * 设置基本类
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2015-2019 ShuangYa
 * @license https://syframework.sylibs.com/license.html
 */
namespace Sy\Config;

trait ConfigTrait {
	/**
	 * 魔术方法，方便调用
	 */
	public function __get($k) {
		return $this->get($k);
	}
	public function __isset($k) {
		return $this->has($k);
	}
	public function has($key) {
		return $this->get($key) !== null;
	}
}