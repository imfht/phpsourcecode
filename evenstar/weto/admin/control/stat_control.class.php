<?php

/*
 * Copyright (C) xiuno.com
 */

!defined('FRAMEWORK_PATH') && exit('FRAMEWORK_PATH not defined.');

include BBS_PATH.'admin/control/admin_control.class.php';

class stat_control extends admin_control {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->check_admin_group();
	}
	
	public function on_index() {
		$this->_title[] = '统计信息';
		$this->_nav[] = '<a href="">统计信息</a>';
		
		$startdate = core::gpc('startdate');
		$enddate = core::gpc('enddate');
		
		$time = $_SERVER['time'];
		
		// 本月 上月 曲线图, 30 个节点，补全，默认最近三十天的。
		if(empty($startdate)) {
			$datelist = $statlist = array();
			for($i = 29; $i >= 0; $i--) {
				$date = $time - $i * 86400;
				$s = date('Y-n-j', $date);
				list($y, $n, $j) = explode('-', $s);
				$datelist[] = array('y'=>$y, 'm'=>$n, 'd'=>$j);
				$statlist[] = $this->stat->read($y, $n, $j);
				
				if($i == 0) $enddate = "$y-$n-$j";
				if($i == 29) $startdate = "$y-$n-$j";
			}
		} else {
			$startdate = str_replace('_', '-', $startdate);
			$enddate = str_replace('_', '-', $enddate);
			$starttime = strtotime($startdate);
			$endtime = strtotime($enddate);
			if($endtime < $starttime) $endtime = $starttime;
			if($endtime - $starttime > 30 * 86400) {
				$endtime = $starttime + 86400 * 30;
			}
			$i = 0;
			$datelist = $statlist = array();
			for($date = $starttime; $date < $endtime; $date += 86400) {
				$s = date('Y-n-j', $date);
				list($y, $n, $j) = explode('-', $s);
				$datelist[] = array('y'=>$y, 'm'=>$n, 'd'=>$j);
				$statlist[] = $this->stat->read($y, $n, $j);
				
				if($i == 0) $startdate = "$y-$n-$j";
				if($i == 29) $enddate = "$y-$n-$j";
				$i++;
			}
		}
		
		// 总发帖数
		$postslist = $this->statlist_fmt($statlist, 'posts');
		$threadslist = $this->statlist_fmt($statlist, 'threads');
		$userslist = $this->statlist_fmt($statlist, 'users');
		$newpostslist = $this->statlist_fmt($statlist, 'newposts');
		$newthreadslist = $this->statlist_fmt($statlist, 'newthreads');
		$newuserslist = $this->statlist_fmt($statlist, 'newusers');
		
		// hook admin_stat_index_view_before.php
		
		$this->view->assign('startdate', $startdate);
		$this->view->assign('enddate', $enddate);
		$this->view->assign('statlist', $statlist);
		$this->view->assign('postslist', $postslist);
		$this->view->assign('threadslist', $threadslist);
		$this->view->assign('userslist', $userslist);
		$this->view->assign('newpostslist', $newpostslist);
		$this->view->assign('newthreadslist', $newthreadslist);
		$this->view->assign('newuserslist', $newuserslist);
		$this->view->display('stat_index.htm');
	}
	
	/*
		调用：
		$postslist = statlist_fmt($statlist, 'posts');
		
		返回的数据格式:
		array (
			0=>array('date'=>'4-25', 'title'=>28, 'height'=>28),
			1=>array('date'=>'4-26', 'title'=>100, 'height'=>100),
			2d=>array('date'=>'4-27', 'title'=>90, 'height'=>90),
		);
	*/
	private function statlist_fmt($statlist, $key) {
		$titlelist = $heightlist = $datelist = $return = array();
		if(empty($statlist)) {
			return array();
		}
		$height = 100;
		$titlelist = misc::arrlist_values($statlist, $key);
		$max = max(1, max($titlelist));
		$heightlist = $titlelist;
		foreach($heightlist as $k=>$v) {
			$h = ceil(($v / $max) * $height);
			$date = $k % 2 ? '' : $statlist[$k]['month'].'-'.$statlist[$k]['day'];	// 间隔一天
			$return[] = array('date'=>$date, 'title'=>$titlelist[$k], 'height'=>$h);
		}
		return $return;
	}
	
	//hook admin_stat_control_after.php
}

?>