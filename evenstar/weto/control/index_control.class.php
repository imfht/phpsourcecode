<?php
/*
 * Copyright (C) xiuno.com
 */

!defined('FRAMEWORK_PATH') && exit('FRAMEWORK_PATH not defined.');

include BBS_PATH.'control/common_control.class.php';

class index_control extends common_control {
	private $dateStartTimestamp;
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->_checked['bbs'] = ' class="checked"';
		$this->_title[] = $this->conf['seo_title'] ? $this->conf['seo_title'] : $this->conf['app_name'];
		
		$this->_seo_keywords = $this->conf['seo_keywords'];
		$this->_seo_description = $this->conf['seo_description'];
		
		$dateNow = new DateTime();
		$dateStart = $dateNow->sub(new DateInterval('P30D'));
		$dateStart->setTime(0, 0, 0);
		$this->dateStartTimestamp = $dateStart->getTimestamp();
	}
	
	// 给插件预留个位置
	public function on_index() {
		// hook index_index_before.php
		
	//	$this->on_bbs();
		$this->on_weto();
	}
	
	/*
	 * by weto
	 * 对top数组先按threads降序然后按posts降序排序
	 */
	private function fids_sort(&$data) {
		foreach ($data as $key => $row) {
			$threads[$key] = $row['threads'];
			$posts[$key] = $row['posts'];
		}
		array_multisort($threads, SORT_DESC, $posts, SORT_DESC, $data);
	}
	
	/*
	 * by weto
	 * 获取热点
	 */
	private function get_hot($name) {
		$threadTypeArr = $this->thread_type->index_fetch(array('typename'=>$name), array(), 0, 1);
		if(empty($threadTypeArr)) return FALSE;
		$threadTypeArr = current($threadTypeArr);
		
		$threadArr = $this->thread->index_fetch(array('fid'=>$threadTypeArr['fid'], 'typeid1'=>$threadTypeArr['typeid'], 'digest'=>3), array('dateline'=>-1), 0, 1);
		if(empty($threadArr)) return FALSE;
		$threadArr = current($threadArr);
		
		$postArr = $this->post->index_fetch(array('fid'=>$threadArr['fid'], 'pid'=>$threadArr['firstpid']));
		if(empty($postArr)) return FALSE;
		$postArr = current($postArr);
		
		$result['fid'] = $threadArr['fid'];
		$result['tid'] = $threadArr['tid'];
		$result['typeid1'] = $threadArr['typeid1'];
		$result['subject'] = $threadArr['subject'];
		$result['message'] = utf8::cutstr_cn($postArr['message'], 200);
		
		return $result;
	}
	
	/*
	 * by weto
	 * 获取首页贴图
	 */
	private function get_images($start=0, $limit=0, $otherConds=array(), $forcedwidth = 192, $forcedheight = 144, $r = 0xF2, $g = 0xF2, $b = 0xF2) {
		$result = array();
		$cond = array('imagenum'=>array('>'=>0));
		if(!empty($otherConds)) {
			foreach ($otherConds as $key=>$value) {
				$cond[$key] = $value;
			}
		}
		
		$imgArr = $this->thread->index_fetch($cond, array('posts'=>-1), $start, $limit);
		if(!empty($imgArr)) {
			foreach ($imgArr as $value) {
				$uploadPath = $this->conf['upload_path'];
				$desImgPath = $uploadPath . 'topImgs/' . date('Y-m-d') . '/';
				!is_dir($desImgPath) && mkdir($desImgPath, 0777, TRUE);
				
				$srcImgPath = $uploadPath . 'attach/';
				$imgInfo = $this->attach->index_fetch(array('tid'=>$value['tid']), array('aid'=>1), 0, 1);
				if(!empty($imgInfo)) {
					$imgInfo = current($imgInfo);
					$srcImgPath .= $imgInfo['filename'];
					$desImgPath .= 'top'. ++$start . strrchr($srcImgPath, '.');
					$imgCreate = image::create_homepage_image($srcImgPath, $desImgPath, $forcedwidth, $forcedheight);
					if(!empty($imgCreate)) {
						$threadType = $this->thread_type->get(array($value['fid'], $value['typeid1']));
						
						$result[$start]['fid'] = $value['fid'];
						$result[$start]['tid'] = $value['tid'];
						$result[$start]['typeid1'] = $value['typeid1'];
						$result[$start]['typename'] = $threadType['typename'];
						$result[$start]['subject'] = $value['subject'];
						$result[$start]['path'] = "./upload/topImgs/". date('Y-m-d') . "/" ."top" . $start . strrchr($srcImgPath, '.');
					}
				}else {
					return FALSE;
				}
			}
		}
		
		return $result;
	}
	
	/* by weto	array_multisort($threads, SORT_DESC, $posts, SORT_DESC, $data);
	 * sort by posts
	 */
	private function sort_by_posts(&$data) {
		if(empty($data)) return;
		foreach ($data as $key=>$value) {
			$posts[$key] = $value['posts'];
		}
		
		array_multisort($posts, SORT_DESC, $data);
	}
	
	/*
	 * by weto
	 * weto首页
	 */
	private function on_weto() {
		$this->_checked['index'] = ' class="checked"';

		// 获取三大幻灯贴图
		$top3ImgsArr = $this->get_images(0, 3, array('lastpost'=>array('>'=>$this->dateStartTimestamp)), 370, 250, 255, 255, 255);
		// 获取精彩贴图
		$niceImgsArr = $this->get_images(3, 4, array('lastpost'=>array('>'=>$this->dateStartTimestamp)));
		
		// 获取主十大及各版块十大（共5个版块，其中:业主之家fid=1; 娱乐健身fid=2; 社会信息fid=3; 系统与热点fid=4; 业主广场fid=5;）
		$top10Arr = array();
		$otherBlocksTopsArr = array();
		for($fid=1; $fid<=5; $fid++) {
			$threadTypeArr = $this->thread_type->index_fetch(array('fid'=>$fid), array(), 0, 0);
			foreach ($threadTypeArr as $threadTypeItem) {
				$key = 'fid=' . $fid . ',typeid=' . $threadTypeItem['typeid'];
				$threadTmpArr = $this->thread->index_fetch(array('fid'=>$fid, 'typeid1'=>$threadTypeItem['typeid'], 'lastpost'=>array('>'=>$this->dateStartTimestamp)), array('posts'=>-1), 0, 1);
				$threadTmpArr = current($threadTmpArr);
				$threadTmpArr['typename'] = $threadTypeItem['typename'];
				if(!isset($threadTmpArr['posts']))		$threadTmpArr['posts'] = 0;
				if(!isset($threadTmpArr['fid']))		$threadTmpArr['fid'] = $fid;
				if(!isset($threadTmpArr['typeid1']))	$threadTmpArr['typeid1'] = $threadTypeItem['typeid'];
				if($fid == 5) {	// 业主广场独立于主十大
					$otherBlocksTopsArr[$fid][$key] = $threadTmpArr;
				}else {
					$top10Arr[$key] = $otherBlocksTopsArr[$fid][$key] = $threadTmpArr;
				}
			}
		}
		
		if(!empty($top10Arr)) {
			$this->sort_by_posts($top10Arr);
			$top10Arr = array_slice($top10Arr, 0, 10);
			
			// 主十大抽出的项目由相应的posts排名第二名来填充
			foreach ($top10Arr as $key => $value) {
				$fidStr = substr($key, 0, strpos($key, ','));
				$fidArr = explode("=", $fidStr);
				$fid = $fidArr[1];
				$typeidStr = substr(strrchr($key, ','), 1);
				$typeidArr = explode("=", $typeidStr);
				$typeid = $typeidArr[1];
				
				$threadTmpArr = $this->thread->index_fetch(array('fid'=>$fid, 'typeid1'=>$typeid, 'lastpost'=>array('>'=>$this->dateStartTimestamp)), array('posts'=>-1), 1, 1);
				$threadTmpArr = current($threadTmpArr);
				$threadTmpArr['typename'] = $value['typename'];
				if(!isset($threadTmpArr['posts']))		$threadTmpArr['posts'] = 0;
				if(!isset($threadTmpArr['fid']))		$threadTmpArr['fid'] = $fid;
				if(!isset($threadTmpArr['typeid1']))	$threadTmpArr['typeid1'] = $typeid;
				$otherBlocksTopsArr[$fid][$key] = $threadTmpArr;
			}
			
			// 各版块排序并取十大
			for($fid=1; $fid<=5; $fid++) {
				if(!empty($otherBlocksTopsArr[$fid])) {
					$this->sort_by_posts($otherBlocksTopsArr[$fid]);
					$otherBlocksTopsArr[$fid] = array_slice($otherBlocksTopsArr[$fid], 0, 10);
				}
			}
		}
		
		// 获取热点
		$localHotArr = $this->get_hot('本地热点');
		$peopleHotArr = $this->get_hot('民生热点');
		
		// 获取其他版块名称
		$blockBoardsArr = array();
		for($fid=1; $fid<=5; $fid++) {
			$threadTypeArr = $this->thread_type->index_fetch(array('fid'=>$fid), array('rank'=>1), 0, 0);
			foreach ($threadTypeArr as $threadTypeItem) {
				$blockBoardsArr[$fid][] = $threadTypeItem;
			}
		}
	
		// 在线会员
		$ismod = ($this->_user['groupid'] > 0 && $this->_user['groupid'] <= 4);
		$this->view->assign('ismod', $ismod);
		
		$this->view->assign_value('fid', 0);
		$this->view->assign_value('isNotHomePage', FALSE);
		$this->view->assign('top3ImgsArr', $top3ImgsArr);
		$this->view->assign('niceImgsArr', $niceImgsArr);
		$this->view->assign('localHotArr', $localHotArr);
		$this->view->assign('peopleHotArr', $peopleHotArr);
		$this->view->assign('top10Arr', $top10Arr);
		$this->view->assign('otherBlocksTopsArr', $otherBlocksTopsArr);
		$this->view->assign('blockBoardsArr', $blockBoardsArr);
		$this->view->display('index_index.htm');
	}
	
	// 首页
	public function on_bbs() {
		$this->_checked['index'] = ' class="checked"';
		
		// hook index_bbs_before.php
		
		$pagesize = 30;
		$toplist = array(); // only top 3
		$readtids = '';
		$page = misc::page();
		$start = ($page -1 ) * $pagesize;
		$threadlist = $this->thread->get_newlist($start, $pagesize);
		foreach($threadlist as $k=>&$thread) {
			$this->thread->format($thread);
			
			// 去掉没有权限访问的版块数据
			$fid = $thread['fid'];
			
			// 那就多消耗点资源吧，谁让你不听话要设置权限。
			if(!empty($this->conf['forumaccesson'][$fid])) {
				$access = $this->forum_access->read($fid, $this->_user['groupid']); // 框架内部有变量缓存，此处不会重复查表。
				if($access && !$access['allowread']) {
					unset($threadlist[$k]);
					continue;
				}
			}
			
			$readtids .= ','.$thread['tid'];
			if($thread['top'] == 3) {
				unset($threadlist[$k]);
				$toplist[] = $thread;
				continue;
			}
		}
		
		$toplist = $page == 1 ? $this->get_toplist() : array();
		$toplist = array_filter($toplist);
		foreach($toplist as $k=>&$thread) {
			$this->thread->format($thread);
            $readtids .= ','.$thread['tid'];
        }
                
		$readtids = substr($readtids, 1); 
		$click_server = $this->conf['click_server']."?db=tid&r=$readtids";
		
		$pages = misc::simple_pages('?index-index.htm', count($threadlist), $page, $pagesize);

		// 在线会员
		$ismod = ($this->_user['groupid'] > 0 && $this->_user['groupid'] <= 4);
		$fid = 0;
		
		$this->view->assign_value('isNotHomePage', FALSE);	// by weto
		
		$this->view->assign('ismod', $ismod);
		$this->view->assign('fid', $fid);
		$this->view->assign('threadlist', $threadlist);
		$this->view->assign('toplist', $toplist);
		$this->view->assign('click_server', $click_server);
		$this->view->assign('pages', $pages);
		
		// hook index_bbs_after.php
		
		$this->view->display('index_index.htm');
	}
	
	public function on_test() {
		$this->view->display('test_drag.htm');
	}
	
	private function get_toplist($forum = array()) {
		$fidtids = array();
		// 3 级置顶
		$fidtids = $this->get_fidtids($this->conf['toptids']);
		
		// 1 级置顶
		if($forum) {
			$fidtids += $this->get_fidtids($forum['toptids']);
		}
		
		$toplist = $this->thread->mget($fidtids);
		return $toplist;
	}
	
	private function get_fidtids($s) {
		$fidtids = array();
		if($s) {
			$fidtidlist = explode(' ', trim($s));
			foreach($fidtidlist as $fidtid) {
				if(empty($fidtid)) continue;
				$arr = explode('-', $fidtid);
				list($fid, $tid) = $arr;
				$fidtids["$fid-$tid"] = array($fid, $tid);
			}
		}
		return $fidtids;
	}
	//hook index_control_after.php
}

?>