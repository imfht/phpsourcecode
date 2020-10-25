<?php

/*
 * Copyright (C) xiuno.com
 */

// 简单方便的 key - value 格式的存储，兼容SAE，便于分布式部署。
class kv extends base_model {
	
	private $data = array();		// 合并存储
	private $changed = array();		// 标示改变的 key
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'kv';
		$this->primarykey = array('k');
		
		// 开启 memcached 加速此 model
		// $this->conf['cache']['enable'] = 1;
		
		//IN_SAE && $this->conf['db']['type'] = 'saekv';
		
		// hook kv_construct_end.php
	}

	/*function __destruct() {
		// $this->xsave(); // 此处 mysql 可能已经被析构，会报错！
	}*/
	
	// 带有过期时间的 get
	public function get($k) {
		$arr = parent::get($k);
		return !empty($arr) && (empty($arr['expiry']) || $arr['expiry'] > $_SERVER['time']) ? core::json_decode($arr['v']) : FALSE;
	}
	
	// 带有过期时间的 set
	public function set($k, $s, $life = 0) {
		$s = core::json_encode($s);
		$arr = array();
		$arr['k'] = $k;
		$arr['v'] = $s;
		$arr['expiry'] = $life ? $_SERVER['time'] + $life : 0;
		return parent::set($k, $arr);
	}
	
	// 合并读取
	public function xget($key = 'conf') {
		$this->data[$key] = $this->get($key);
		return $this->data[$key];
	}
	
	// 合并写入
	public function xset($k, $v, $key = 'conf') {
		if(!isset($this->data[$key])) {
			$this->data[$key] = $this->get($key);
		}
		$this->data[$key][$k] = $v;
		$this->changed[$key] = 1;
	}
	
	// 删除某个
	public function xunset($k, $key = 'conf') {
		if(!isset($this->data[$key])) {
			unset($this->data[$key]);
		}
		$this->changed[$key] = 1;
	}
	
	// 显示的保存
	public function xsave($key = 'conf') {
		$this->set($key, $this->data[$key]);
		$this->changed[$key] = 0;
	}
	
	// 保存改变的 key
	public function save_changed() {
		foreach($this->changed as $key=>$v) {
			$v && $this->xsave($key);
		}
	}
	
	// 删除一个 key, 
	// $arg2 = FALSE, $arg3 = FALSE, $arg4 = FALSE 仅仅为了兼容 base_model, 没有意义
	public function delete($k, $arg2 = FALSE, $arg3 = FALSE, $arg4 = FALSE) {
		return parent::delete($k);
	}

}
?>