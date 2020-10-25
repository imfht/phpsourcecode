<?php
/**
 * 模板引擎接口
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Library
 * @link https://www.sylibs.com/
 * @copyright Copyright (c) 2015-2019 ShuangYa
 * @license https://syframework.sylibs.com/license.html
 */
namespace Sy\Http;

interface TemplateInterface {
	/**
	 * Construct
	 * 
	 * @access public
	 */
	public function __construct();
	/**
	 * 清除已声明的模板变量
	 * 
	 * @access public
	 */
	public function clearAssign();
	/**
	 * 声明模板变量
	 * 
	 * @access public
	 * @param string $name
	 * @param mixed $value
	 */
	public function assign(string $name, $value);
	/**
	 * 进行渲染
	 * 
	 * @access public
	 * @param string $path 模板路径
	 * @return string
	 */
	public function render(string $path): string;
}
