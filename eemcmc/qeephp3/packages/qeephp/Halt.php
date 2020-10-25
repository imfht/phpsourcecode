<?php namespace qeephp;

/**
 * 对register_shutdown_function 函数栈的扩展
 */
class Halt {
	
	private $stack = array();
	
	private function __construct()
	{
		register_shutdown_function(array($this, '__flush'));
	}
	
	/**
	 * @return \qeephp\Halt
	 */
	static function getInstance()
	{
		static $var = null;
		if (is_null($var)) $var = new self();
		return $var;
	}
	
	/**
	 * 向框架注册一个脚本结束时要调用的方法
	 * 此方法用于取代 类的 析构函数,当脚本运行出现异常或者错误时,析构函数并不执行
	 * 
	 * 注入的方法中 不能 带 exit | die 函数,不然会打断堆栈的执行
	 * 
	 * $callback 类型:
	 * 	  1. 字符串 -- str_func_name
	 * 	  2. 数组   -- array($class_or_obj,$method) 
	 * @param mixed $callback
	 * @param array $params
	 */
	function add($callback,array $params=null)
	{
		if (is_callable($callback))
    		$this->stack[] = array($callback,$params ? $params : array());
	}
	
	/**
	 * 执行时遵循"后进先出"的方式来执行函数
	 */
	function __flush()
	{	
		while(!empty($this->stack)){
			list($callback,$params) = array_pop($this->stack);			
			call_user_func_array($callback,$params);
		}
	}
}