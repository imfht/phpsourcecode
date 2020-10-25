<?php
/**
 * 模板引擎接口
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Interface
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace YesfApp\Http;

use Yesf\Http\TemplateInterface;

class CustomEngine implements TemplateInterface {
	public $prefix = '';
	public $vars = [];
	public function __construct() {
	}
	public function setPrefix($str) {
		$this->prefix = $str;
	}
	public function clearAssign() {
		$this->vars = [];
	}
	public function assign(string $name, $value) {
		$this->vars[$name] = $value;
	}
	public function render(string $path): string {
		$result = file_get_contents($path);
		$vars = $this->vars;
		$prefix = $this->prefix;
		return preg_replace_callback('/\{\{(\w+)\}\}/', function($matches) use (&$vars, &$prefix) {
			return isset($vars[$matches[1]]) ? $prefix . $vars[$matches[1]] : '';
		}, $result);
	}
}
