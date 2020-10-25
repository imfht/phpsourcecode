<?php

/*
 * Copyright (C) xiuno.com
 */

// 采用文件缓存，不利于分布式部署。正式版废弃。

/*
	正常版本，保存到 kv table， json 格式。
	SAE 保存数据到 kvdb://
	详情参看 model/kv.class.php
*/

class mcache extends base_model {
	
	private $vars;// 缓存已经加载的数据
	
	function __construct(&$conf) {
		parent::__construct($conf);
		
		// hook mcache_construct_end.php
	}

	/*
		$arg3, $arg4, 用来兼容 base_model.update() 方法参数个数。没有用处。
	*/
	public function read($cachename, $arg = NULL, $arg3 = FALSE, $arg4 = FALSE) {
		$key = $cachename.'_'.$arg;
		if(isset($this->vars[$key])) {
			return $this->vars[$key];// 避免重复加载，此处提升效率明显！
		}
		
		$data = $this->real_get($cachename, $arg);
		if(empty($data)) {
			$data = $this->_update($cachename, $arg);
		}
		$this->vars[$key] = $data;
		return $data;
	}
	
	public function clear($cachename, $arg = NULL) {
		return $this->real_delete($cachename, $arg);
	}
	
	// 格式化以后的数据存入 cache
	private function get_forum($fid) {
		$forum = $this->forum->read($fid);
		$forum && $this->forum->format($forum);
		$forum && $this->forum->format_thread_type($forum);
		
		// hook mcache_model_forum_end.php
		
		return $forum;
	}
	
	public function real_get($cachename, $arg) {
		$k = "cache_{$cachename}_$arg";
		return $this->kv->get($k);
	}
	
	public function real_delete($cachename, $arg) {
		$k = "cache_{$cachename}_$arg";
		$this->kv->delete($k);
		return TRUE;
	}
	
	public function real_set($cachename, $arg, $data) {
		$k = "cache_{$cachename}_$arg";
		$this->kv->set($k, $data);
		return TRUE;
	}
	
	private function _update($cachename, $arg) {
		$method = "get_$cachename";
		if(method_exists($this, $method)) {
			$data = $this->$method($arg);
			if(empty($data)) {
				return array();// 强行返回，不保存到文件
			}
		} else {
			throw new Exception('cache_model: '.$method.' does not exists');
		}
		$this->real_set($cachename, $arg, $data);
		return $data;
	}
	
	// hook mcache_model_end.php
	
}

?>