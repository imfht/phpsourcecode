<?php
/**
 * 队列存储基类
 */
abstract class CStore implements IStore{
	/**
	 * 本次缓存操作的 key
	 */
	private $_key;
	
	public function __construct() {
		$this->init();
	}
	
	public function init() {
		return true;
	}
	
	/**
	 * 组合键，加上前缀等
	 */
	public function combinationKey() {
		$prefix = QueueConfig::instance()->STORE_PREEFIX;
		
		if(empty($prefix)) return;
		if(!is_array($this->_key))
			$this->_key = $this->combination($prefix, $this->_key);
		else
			foreach($this->_key as &$item) {
				$item = $this->combination($prefix, $item);
			}
	}
	
	/**
	 * 将多个参数用 '_' 链接
	 */
	public function combination() {
		return implode('_', func_get_args());
	}
	
	/**
	 * 设置本次缓存操作的 key
	 */
	public function setKey($key) {
		$this->_key = $key;
		$this->combinationKey();
		$this->afterSetKey();
		return $this;
	}
	
	/**
	 * 获取本次缓存操作的 key
	 */
	public function getKey() {
		return $this->_key;
	}
	
	/**
	 * 在setKey 之后自动调用
	 */
	public function afterSetKey() {
		
	}
}