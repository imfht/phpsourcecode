<?php

/**
 * Created by PhpStorm.
 * Author: William
 * Date: 2016/9/12
 * Time: 20:38
 */

/**
 * Class Loader
 * Loader类，主要负责整个系统组件所有类的加载，统一保存在Loader的属性当中
 */
class Loader
{
	public $is_loaded = array();
	public $component = array();
	public $functional = array();

	public function __construct($config)
	{
		//自动加载组件
		foreach ($config['auto_load'] as $key => $component) {
			$this->component($component);
		}
		$this->config->setArray($config);
	}

	/**
	 * 类名预处理
	 * @param string $className
	 */
	private function preHandle($className = ''){
		return ucfirst(strtolower($className));
	}
	/**
	 * 加载组件类
	 * @param string $className 类名
	 * @return boolean
	 */
	public function component($className = '')
	{
		$class =$this->preHandle($className);

		$file = BASEPATH . 'Component/' . $class . '.php';
		if(in_array($file,$this->is_loaded)){
			return TRUE;
		}
		if (file_exists($file)) {
			require_once($file);

			$this->is_loaded[] = $file;
			$this->$className=new $class();
			return TRUE;
		} else {
			die($className . '不存在');
		}
	}

	public function functional($className = '')
	{
		$class = $this->preHandle($className);

		$file = BASEPATH . 'Functional/' .$class . '.php';
		if(in_array($file,$this->is_loaded)){
			return TRUE;
		}
		if (file_exists($file)) {
			require_once($file);

			$this->is_loaded[] = $file;
			$this->$className=new $class();
			return TRUE;
		} else {
			die($className . '不存在');
		}
	}

	/**
	 * 返回component对象
	 * @param $className
	 * @return object
	 */
	public function getComponent($className){
		return $this->$className;
	}

	/**
	 * 返回functional对象
	 * @param $className
	 * @return object
	 */
	public function getFunctional($className){
		return $this->$className;
	}


}