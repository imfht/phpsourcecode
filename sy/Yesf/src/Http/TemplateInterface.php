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
namespace Yesf\Http;

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
