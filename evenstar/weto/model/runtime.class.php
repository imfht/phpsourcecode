<?php

/*
 * Copyright (C) xiuno.com
 */

// runtime 运行产生的数据，如果DB压力大，可以独立成服务，此表暂时只有一条数据。
// 不同于 kv, 它是内存数据，是可以被清空的，可以狭义的理解为 memcached

class runtime extends base_model {
	
	private $data = array();		// 合并存储
	private $changed = array();		// 标示改变的 key
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'runtime';
		$this->primarykey = array('k');
		
		//IN_SAE && $this->conf['db']['type'] = 'saekv';
		
		// hook runtime_construct_end.php
	}
	
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
	
	// 合并读取，一次读取多个，增加效率
	public function xget($key = 'runtime') {
		if(isset($this->data[$key])) {
			return $this->data[$key];
		} else {
			$this->data[$key] = $this->get($key);
			if($key == 'runtime' && empty($this->data[$key])) {
				// 冗余存储了 toptids, 在 runtime 数据丢失的时候，可以恢复。
				$toptids = $this->kv->get('toptids');
				$this->data[$key] = (array)$this->kv->get('conf');
				
				$forumlist = $this->forum->get_list();
				$forumarr = misc::arrlist_key_values($forumlist, 'fid', 'name');
				$grouplist = $this->group->get_list();
				$grouparr = misc::arrlist_key_values($grouplist, 'groupid', 'name');
				$forumaccesson = $this->forum_access->get_accesson($forumarr);
				$this->data[$key] += array (
					'onlines'=>$this->online->index_count(),
					'posts'=>$this->post->count(),
					'threads'=>$this->thread->count(),
					'users'=>$this->user->count(),
					'todayposts'=>0,
					'todayusers'=>0,
					'cron_1_next_time'=>0,
					'cron_2_next_time'=>0,
					'newuid'=>0,
					'newusername'=>'',
					'toptids'=>$toptids,
					'forumarr'=>$forumarr,
					'forumaccesson'=>$forumaccesson,
					'grouparr'=>$grouparr,
				);
				$this->set('runtime', $this->data[$key]);
			}
			return $this->data[$key];
		}
	}
	
	public function xset($k, $v, $key = 'runtime') {
		if(!isset($this->data[$key])) {
			$this->data[$key] = $this->xget($key);
		}
		if($v && is_string($v) && ($v[0] == '+' || $v[0] == '-')) {
			$v = intval($v);
			$this->data[$key][$k] += $v;
		} else {
			$this->data[$key][$k] = $v;
		}
		$this->changed[$key] = 1;
	}
	
	// 删除某个
	public function xunset($k, $key = 'runtime') {
		if(!isset($this->data[$key])) {
			unset($this->data[$key]);
		}
		$this->changed[$key] = 1;
	}
	
	// 更新
	public function xupdate($k) {
		if($k == 'forumarr') {
			$forumlist = $this->forum->get_list();
			$forumarr = misc::arrlist_key_values($forumlist, 'fid', 'name');
			$forumaccesson = $this->forum_access->get_accesson($forumarr);
			$this->xset('forumarr', $forumarr);
			$this->xset('forumaccesson', $forumaccesson);
		} elseif($k == 'grouparr') {
			$grouplist = $this->group->get_list();
			$grouparr = misc::arrlist_key_values($grouplist, 'groupid', 'name');
			$this->xset('grouparr', $grouparr);
		}
	}
	
	// 显示的保存
	public function xsave($key = 'runtime') {
		$this->set($key, isset($this->data[$key]) ? $this->data[$key] : '');
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