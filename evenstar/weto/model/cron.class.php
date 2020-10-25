<?php

/*
 * Copyright (C) xiuno.com
 */

class cron extends base_model {

	function __construct(&$conf) {
		parent::__construct($conf);
		//set_time_limit(600);
		//ignore_user_abort(true);
		
		// hook cron_construct_end.php
	}
	
	public function run() {
		$cron_1_next_time = $this->conf['cron_1_next_time'];
		$cron_2_next_time = $this->conf['cron_2_next_time'];
		
		$time = $_SERVER['time'];
			
		// 跨多台WEB的锁。
		if($this->runtime->get('cronlock') == 1) {
			// 判断锁是否过期?
			if($time > $cron_1_next_time + 30) {
				// 过期则解锁
				//$this->runtime->set('cronlock', 0);
			} else {
				// 否则表示其他进程正在执行
				return;
			}
		}
		$this->runtime->set('cronlock', 1);
	
		// 5 分钟执行一次
		if($time > $cron_1_next_time) {
			$nexttime = $time + 300;
			$this->runtime->xset('cron_1_next_time', $nexttime);
			
			// gc online table
			$this->online->gc();
			// $this->runtime->xsave();
			
			log::write('cron_1_next_time:'.date('Y-n-j H:i', $nexttime), 'cron.php');
			
			// hook cron_model_run_1_end.php
		}
		
		// execute on 0:00 perday.
		if($time > $cron_2_next_time) {
			
			// update the next time of cron
			$nexttime = $_SERVER['time_today'] + 86400;
			$this->runtime->xset('cron_2_next_time', $nexttime);
			log::write('cron_2_next_time:'.date('Y-n-j H:i', $nexttime), 'cron.php');
			
			// set todayposts zero.
			$forumlist = $this->forum->get_list();
			foreach($forumlist as $forum) {
				$forum['todayposts'] = 0;
				$this->forum->xupdate($forum);
			}
			
			
			
			// 统计
			$arr = explode(' ', $_SERVER['time_fmt']);
			list($y, $n, $d) = explode('-', $arr[0]);
			
			$stat = $this->stat->read($y, $n, $d);
			if(empty($stat)) {
				$threads = $this->thread->count();
				$posts = $this->post->count();
				$users = $this->user->count();
				$stat = array (
					'year'=>$y,
					'month'=>$n,
					'day'=>$d,
					'threads'=>$threads,
					'posts'=>$posts,
					'users'=>$users,
					'newposts'=>$this->conf['todayposts'],
					'newusers'=>$this->conf['todayusers'],
				);
				$this->stat->create($stat);
			}
		
			// 清理最新主题，超过100篇，则开始清理，保留2天内的数据，方便 sphinx 搜索引擎做增量索引
			if($this->thread_new->count() > 100) {
				// 查找两天内的数据是否足够100条，不足则不予处理
				$n = $this->thread_new->index_count(array('dateline' => array('>'=>$_SERVER['time'] - 86400 * 2)));
				if($n > 100) {
					$this->thread_new->index_delete(array('dateline' => array('<'=>$_SERVER['time'] - 86400 * 2)));
				}
			}
			
			// 清空
			$n = $this->online->index_count();
			$this->runtime->xset('todayposts', 0);
			$this->runtime->xset('todayusers', 0);
			$this->runtime->xset('onlines', $n);	// 校对
			$this->conf['onlines'] = $n;
			$this->conf['todayposts'] = 0;
			$this->conf['todayusers'] = 0;
			// $this->runtime->xsave();
			
			// 清理未关联的垃圾
			$this->attach->gc();
			
			// hook cron_model_run_2_end.php
		}
		
		// 释放锁
		$this->runtime->set('cronlock', 0);
		
		//$this->runtime->xsave('runtime');
		// hook cron_model_run_end.php
		
	}
}
?>