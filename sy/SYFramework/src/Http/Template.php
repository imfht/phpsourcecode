<?php
/**
 * 默认模板引擎
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Library
 * @link https://www.sylibs.com/
 * @copyright Copyright (c) 2015-2019 ShuangYa
 * @license https://syframework.sylibs.com/license.html
 */
namespace Sy\Http;

class Template implements TemplateInterface {
	private $vars = [];
	public function __construct() {
	}
	public function clearAssign() {
		$this->vars = [];
	}
	public function assign(string $name, $value) {
		$this->vars[$name] = $value;
	}
	public function render(string $_full_path): string {
		extract($this->vars, EXTR_SKIP);
		ob_implicit_flush(false);
		ob_start();
		if (is_file($_full_path)) {
			include($_full_path);
		}
		return ob_get_clean();
	}
	public function __clone() {
		$this->clearAssign();
	}
}