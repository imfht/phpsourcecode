<?php

/*
 * Copyright (C) xiuno.com
 */

class online extends base_model {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'online';
		$this->primarykey = array('sid');
		
		// hook online_construct_end.php
	}
	
	// 重载, 因为 online 表为 Memory 类型，重启后消失，count 不准确。
	public function count($val = FALSE) {
		return $this->index_count();
	}
	
	// 最多400个会员，再多没啥意义了，耗费带宽
	public function get_onlinelist($limit = 400) {
		$onlinelist = $this->index_fetch(array('uid'=>array('>'=>0)), array(), 0, $limit);
		foreach($onlinelist as &$online) {
			$this->format($online);
		}
		return $onlinelist;
	}
	
	// 用来显示给用户
	public function format(&$user) {
		// format data here.
		$user['lastvisit_fmt'] = misc::humandate($user['lastvisit']);
	}
	
	// cron_1_next_time，每隔5分钟执行一次，首页缓存也会被刷新。
	public function gc() {
		// 默认 15 分钟算离线
		$expiry = $_SERVER['time'] - $this->conf['online_hold_time'];
		
		// 采用暴力的 index_delete() 节省代码，有可能删除自己！
		$n = $this->index_delete(array('lastvisit'=>array('<'=>$expiry)));
		if($n <= 0) return;
		
		$this->conf['onlines'] -= $n;
		//log::trace("n: $n, onlines: ".$this->conf['onlines']);
		// 修正非法数据：意外
		if($this->conf['onlines'] < 1) {
			$n = $this->index_count();
			$this->runtime->xset('onlines', $n);
		} else {
			$this->runtime->xset('onlines', $this->conf['onlines']);
		}
		
		$this->runtime->xsave();
	}
}
?>