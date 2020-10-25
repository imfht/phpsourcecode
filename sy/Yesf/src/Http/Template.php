<?php
/**
 * 默认模板引擎
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Http;

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