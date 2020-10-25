<?php

/**
 * Hook接口
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Interface
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015-2016 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=framework&type=license
 */

namespace sy\interfaces;

interface hook {
	//以字符串表示的Hook类型
	public $type;
	//以字符串表示的Hook名称
	public $name;
	/**
	 * 被添加到Hook列表时运行的函数
	 * 可以进行一些初始化操作
	 * @access public
	 */
	public function add();
	
	/**
	 * 触发条件时运行的函数
	 * @access public
	 */
	public function run();
}