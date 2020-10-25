<?php

/*
 * Copyright (C) xiuno.com
 */

// 此 model 不需要 maxid, count
class thread_type extends base_model {
	
	// 最多三种主题分类，支持复合查询，1, 2, 3, 1-2, 2-3, 1-3, 1-2-3
	public $map = array (
		1 => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40),
		2 => array(41, 82, 123, 164, 205, 246, 287, 328, 369, 410, 451, 492, 533, 574, 615, 656, 697, 738, 779, 820, 861, 902, 943, 984, 1025, 1066, 1107, 1148, 1189, 1230, 1271, 1312, 1353, 1394, 1435, 1476, 1517, 1558, 1599, 1640),
		3 => array(1681, 3362, 5043, 6724, 8405, 10086, 11767, 13448, 15129, 16810, 18491, 20172, 21853, 23534, 25215, 26896, 28577, 30258, 31939, 33620, 35301, 36982, 38663, 40344, 42025, 43706, 45387, 47068, 48749, 50430, 52111, 53792, 55473, 57154, 58835, 60516, 62197, 63878, 65559, 67240),
		4 => array(136161, 205082, 274003, 342924, 411845, 480766, 549687, 618608, 687529, 756450, 825371, 894292, 963213, 1032134, 1101055, 1169976, 1238897, 1307818, 1376739, 1445660, 1514581, 1583502, 1652423, 1721344, 1790265, 1859186, 1928107, 1997028, 2065949, 2134870, 2203791, 2272712, 2341633, 2410554, 2479475, 2548396, 2617317, 2686238, 2755159, 2824080),
	);
	
	/*
		tid = 100
		typeid = 12 205 1681
		
		100 12
		100 205
		100 1681
		100 12+205
		100 205+1681
		100 12+1681
		100 12+205+1681
	*/
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'thread_type';
		$this->primarykey = array('fid', 'typeid');
		
		// hook thread_type_construct_end.php
	}
	
	private function find_empty_typeid($fid, $cateid) {
		// 最多 40 次查询
		foreach($this->map[$cateid] as $id) {
			$r = $this->read($fid, $typeid);
			if(empty($r)) return $r;
		}
		return 0;
	}
	
	// 删除版块的时候清除
	public function delete_by_fid($fid) {
		$arrlist1 = $this->get_list_by_fid_cateid($fid, 1);
		$arrlist2 = $this->get_list_by_fid_cateid($fid, 2);
		$arrlist3 = $this->get_list_by_fid_cateid($fid, 3);
		$arrlist4 = $this->get_list_by_fid_cateid($fid, 4);
		$arrlist = array_merge($arrlist1, $arrlist2, $arrlist3, $arrlist4);
		foreach($arrlist as $arr) {
			$this->delete($arr['fid'], $arr['typeid']);
		}
	}
	
	public function get_typename($fid, $typeid) {
		$arr = $this->get($fid, $typeid);
		return empty($arr) ? '' : $arr['typename'];
	}
	
	// 要么为空，要么为补全数组
	public function get_list() {
		$typelist = $this->index_fetch(array(), array(), 0, 0);
		foreach($typelist as $k=>$v) {
			if(empty($v['typename'])) {
				unset($typelist[$k]);
			}
		}
		return $typelist;
	}
	
	// 获取所有的 thread_type, 2.0.3 2013/6/14 以后废弃此方法
	public function get_typearr_from_cache() {
		// 主题分类数据
		$typearr = $this->runtime->get('typearr');
		if($typearr === FALSE) {
			$threadtypelist = $this->get_list();
			$typearr = array();
			misc::arrlist_multisort($threadtypelist, 'rank', 1);
			foreach($threadtypelist as $threadtype) {
				$typearr[$threadtype['fid']][$threadtype['typeid']] = $threadtype['typename'];
			}
			$this->runtime->set('typearr', $typearr, 600000); // todo:一分钟的缓存时间！这里可以根据负载进行调节。
		}
		return $typearr;
	}
	
	// 2.0.3 2013/6/14 以后启用此方法
	public function get_types_from_cache() {
		// 主题分类数据
		$types = $this->runtime->get('types');
		if($types === FALSE) {
			// 所有板块的主题分类数据
			$forumarr = $this->conf['forumarr'];
			foreach($forumarr as $fid=>$forumname) {
				$forum = $this->forum->read($fid);
				$this->forum->format_thread_type($forum);
				$types[$fid] = $forum['types'];
			}
			$this->runtime->set('types', $types, 600000); // todo:一分钟的缓存时间！这里可以根据负载进行调节。
		}
		return $types;
	}
	
	// 返回非空的主题分类, cateid = 1, 2, 3，排序？
	/*
		返回格式，按照 rank 正序，typeid 为 key：
		array(
			3=>array('typeid'=>3, 'rank'=>1, 'enable'=>1, 'typename'=>'主题分类3'),
			2=>array('typeid'=>2, 'rank'=>2, 'enable'=>1, 'typename'=>'主题分类2'),
			1=>array('typeid'=>1, 'rank'=>3, 'enable'=>1, 'typename'=>'主题分类1'),
			4=>array('typeid'=>4, 'rank'=>4, 'enable'=>1, 'typename'=>'主题分类4'),
		)
	*/
	// 要么为空，要么为补全数组
	public function get_list_by_fid_cateid($fid, $cateid, $fillblank = TRUE) {
		$typelist = array();
		foreach($this->map[$cateid] as $typeid) {
			$arr = $this->read($fid, $typeid);
			if(empty($arr)) {
				$fillblank && $arr = array('fid'=>$fid, 'typeid'=>$typeid, 'typename'=>'', 'rank'=>$typeid, 'enable'=>0);
			}
			if($arr) {
				$typelist[$typeid] = $arr;
			}
		}
		if($typelist) {
			misc::arrlist_multisort($typelist, 'rank', TRUE);	// 排序后key 丢失
			misc::arrlist_change_key($typelist, 'typeid');		// 找回 key
		}
		return $typelist;
	}
	
	// ------------------> 杂项
	
	// 用来显示给用户
	public function format(&$type) {
		
	}
	
	public function check_typeid(&$typeid, $cateid) {
		$cateid == 1 && $typeid = $typeid >= 1 && $typeid <= 40 ? $typeid : 0;
		$cateid == 2 && $typeid = $typeid >= 41 && $typeid <= 1640 ? $typeid : 0;
		$cateid == 3 && $typeid = $typeid >= 1681 && $typeid <= 67240 ? $typeid : 0;
		$cateid == 4 && $typeid = $typeid >= 136161 && $typeid <= 2824080 ? $typeid : 0;
	}
	
}
?>