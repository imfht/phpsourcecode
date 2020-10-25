<?php
namespace OT;

/**
* 文件创建
*/
class Builder{

    // 实例化对象
    public $_class;
	
	public function __construct($class = 'List',$method=''){
		$identify   =   $class.$method;
		$type = $class;
		$class  =   strpos($class,'\\')? $class: 'OT\\Builder\\Driver\\'. ucwords(strtolower($class));
		$class = $class."Builder";
        if(!isset($this->_class)) {
			if(class_exists($class)){
				$o   =   new $class($type);
	            if(!empty($method) && method_exists($o,$method))
	                $this->_class = call_user_func(array(&$o, $method));
	            else
	                $this->_class = $o;
			}else{
				// 类没有定义
				E(L('_CLASS_NOT_EXIST_').': ' . $class);
			}
		}
		return $this->_class;
	}

    public function __call($method, $arguments) {
        if (method_exists($this, $method)) {
            return call_user_func_array(array(&$this, $method), $arguments);
        } elseif (!empty($this->_class) && method_exists($this->_class, $method)) {
            return call_user_func_array(array(&$this->_class, $method), $arguments);
        }
    }
}