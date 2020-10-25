<?php

/**
 * Coltroller基本类
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

abstract class Controller {
	public $_sy_module = '';
	protected $_sy_tpl_vars = [];
	/**
	 * 加载Model
	 * @access protected
	 * @param string $modelName
	 * @param string $loadAs
	 */
	protected function loadModel($modelName, $loadAs) {
		$modelName .= 'Model';
		$this->{$loadAs} = $modelName::getInstance();
	}
	/**
	 * 注册模板变量
	 * @access protected
	 * @param string $key
	 * @param mixed $value
	 */
	protected function assign($key, $value) {
		$this->_sy_tpl_vars[$key] = $value;
	}
	/**
	 * 渲染模板
	 * @access public
	 * @param string $name
	 */
	protected function display($name) {
		if (Sy::$app->get('csrf')) {
			$this->_sy_tpl_vars['_csrf_token'] = \sy\lib\Security::csrfGetHash();
		}
		$_sy_fullPath = Sy::$appDir . 'modules/' . $this->_sy_module . '/views/' . $name . '.phtml';
		extract($this->_sy_tpl_vars, EXTR_SKIP);
		if (is_file($_sy_fullPath)) {
			include($_sy_fullPath);
		}
	}
}
