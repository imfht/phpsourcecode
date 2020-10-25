<?php

/*
 * Copyright (C) xiuno.com
 */

class attach extends base_model {
	
	// view/image/filetype/xxx.gif
	// 以下文件名后缀将直接存放于服务器上。白名单机制，避免安全风险。doc 不知道有没有可能造成跨站。
	public $filetypes = array(
		'av' => array('av', 'wmv', 'wav', 'wma', 'avi'),
		'real' => array('rm', 'rmvb'),
		'mp3' => array('mp3','mp4'),
		'binary' => array('dat', 'bin'),
		'flash' => array('swf', 'fla', 'as'),
		'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
		'office' => array('doc', 'xls', 'ppt'),
		'pdf' => array('pdf'),
		'text' => array('c', 'cpp', 'cc'),
		'zip' => array('tar', 'zip', 'gz', 'tar.gz', 'rar', '7z', 'bz'),
		'book' => array('chm'),
		'torrent' => array('bt', 'torrent'),
		'font' => array('ttf', 'font', 'fon')
	);
	
	//public $safe_exts = array ();
	
	/* sx ie 会解析 .jpg .gif .txt 中的 <script>，彻底无语了！
	public $safe_exts = array (
		'av', 'wmv', 'wav', 'wma', 'avi', 
		'rm', 'rmvb',
		'mp3','mp4',
		'dat', 'bin',
		'fla', 'as',
		'gif', 'jpg', 'jpeg', 'png', 'bmp',
		'txt', 'c', 'cpp', 'cc',
		'tar', 'zip', 'gz', 'tar.gz', 'rar', '7z', 'bz',
		'bt', 'torrent'
	);*/
	
	// 只保留几种压缩格式，其他都去掉。另外图片要过滤 <script
	public $safe_exts = array (
		'tar', 'zip', 'gz', 'tar.gz', 'rar', '7z', 'bz',
	);
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->table = 'attach';
		$this->primarykey = array('fid', 'aid');
		$this->maxcol = 'aid';
		
		// hook attach_construct_end.php
	}
	
	public function get_allow_filetypes() {
		$arr = array();
		foreach($this->filetypes as $v) {
			$arr = array_merge($arr, $v);
		}
		return implode(' ', $arr);
	}
	
	public function get_list_by_fid_pid($fid, $pid, $isimage = 0) {
		$attachlist = $this->index_fetch(array('fid'=>$fid, 'pid'=>$pid), array(), 0, 1000);
		foreach($attachlist as $k=>$attach) {
			if($isimage == 1 && $attach['isimage'] == 0) {
				unset($attachlist[$k]);
				continue;
			}
			if($isimage == 0 && $attach['isimage'] == 1) {
				unset($attachlist[$k]);
				continue;
			} 
			$this->format($attachlist[$k]);
		}
		misc::arrlist_multisort($attachlist, 'aid', TRUE);
		return $attachlist;
	}
	
	public function get_list_by_uid($uid, $page = 1, $pagesize = 20) {
		$start = ($page - 1) * $pagesize;
		$attachlist = $this->index_fetch(array('uid'=>$uid, 'isimage'=>0), array(), $start, $pagesize);
		foreach($attachlist as &$attach) {
			$this->format($attach);
		}
		misc::arrlist_multisort($attachlist, 'aid', FALSE);
		return $attachlist;
	}
	
	public function get_imagelist_by_uid($uid, $page = 1, $pagesize = 20) {
		$start = ($page - 1) * $pagesize;
		$attachlist = $this->index_fetch(array('uid'=>$uid, 'isimage'=>1), array(), $start, $pagesize);
		foreach($attachlist as &$attach) {
			$this->format($attach);
		}
		misc::arrlist_multisort($attachlist, 'aid', FALSE);
		return $attachlist;
	}
	
	public function xdelete($fid, $pid) {
		$attachlist = $this->index_fetch(array('fid'=>$fid, 'pid'=>$pid), array(), 0, 10000);
		foreach($attachlist as $attach) {
			$this->unlink($attach);
			$this->delete($fid, $attach['aid']);
		}
		$n = count($attachlist);
		// hook attach_model_xeelete_end.php
		return $n;
	}
	
	public function unlink($attach) {
		$filepath = $this->conf['upload_path'].'attach/'.$attach['filename'];
		if($attach['filetype'] == 'image') {
			$attachthumb = image::thumb_name($filepath);
			is_file($attachthumb) && unlink($attachthumb);
		}
		is_file($filepath) && unlink($filepath);
		// hook attach_model_unlink_end.php
	}
	
	// 根据文件名判断文件类型
	public function get_filetype($filename) {
		$filename = strtolower($filename);
		$ext = substr(strrchr($filename, '.'), 1);
		foreach($this->filetypes as $type=>$arr) {
			if(in_array($ext, $arr)) {
				return $type;
			}
		}
		return 'unknown';
	}
	
	public function get_filehtml($filename, $filetype, $fileurl) {
		$filename = htmlspecialchars(substr($fileurl, strrpos($fileurl, '/') + 1));
	}
	
	// 用来显示给用户
	public function format(&$attach) {
		// format data here.
		if(empty($attach)) return;
		$attach['filesize_fmt'] = misc::humansize($attach['filesize']);
		$attach['orgfilename_fmt'] = utf8::substr($attach['orgfilename'], 0, 12);
		$attach['dateline_fmt'] = misc::humandate($attach['dateline']);
		$attach['forumname'] = $attach['fid'] ? $this->conf['forumarr'][$attach['fid']] : '';
		$attach['incomes'] = $attach['golds'] * $attach['downloads'];
		if($attach['isimage']) {
			$attach['filename_thumb'] = substr($attach['filename'], 0, -4).'_thumb.jpg';
		}
		// hook attach_model_format_end.php
	}
	
	// 过滤图片中的 <script
	public function is_safe_image($filename) {
		$s = file_get_contents($filename);
		if(strpos($s, '<script') !== FALSE) {
			unset($s);
			return FALSE;
		}
		unset($s);
		return TRUE;
	}
	
	// 返回安全的后缀名: .php .jsp 返回 ._php ._jsp
	public function safe_ext($ext) {
		if(in_array(substr($ext, 1), $this->safe_exts)) {
			return $ext;
		}
		$s = preg_replace('#[^\w]#i', '_', substr($ext, 1));
		$s = '._'.substr($s, 0, 3);	// ._php 这样是否所有 web server 安全需要测试。
		return $s;
	}
	
	public function get_upload_max_filesize() {
		if(function_exists('ini_get') ) {
			$m = ini_get('upload_max_filesize');
			$n = strlen($m);
			if($n > 0) {
				$m[$n - 1] == 'MB' && $m = intval($m) * 1000000;
				$m[$n - 1] == 'KB' && $m = intval($m) * 1000;
			} else {
				$m = 2000000;
			}
		} else {
			$m = 2000000;
		}
		return $m;
	}
	
	
	public function get_uploading_imagelist($uid, $filter = TRUE) {
		$imagelist = array();
		$last = array('pid'=>0);
		$start = 0;
		$limit = 20;
		while(!empty($last) && $last['pid'] == 0) {
			$arrlist = $this->index_fetch(array('uid'=>$uid, 'isimage'=>1), array('aid'=>-1), $start, $limit);
			if(empty($arrlist)) break; //  || count($imagelist)
			($last = array_pop($arrlist)) && array_push($arrlist, $last);
			$imagelist += $arrlist;
			$start += $limit;
		}
		if($filter) {
			foreach($imagelist as $k=>$attach) {
				if(!isset($attach['pid']) || $attach['pid'] > 0) {
					unset($imagelist[$k]);
				}
			}
		}
		return $imagelist;
	}
	
	public function get_uploading_attachlist($uid, $filter = TRUE) {
		$attachlist = array();
		$last = array('pid'=>0);
		$start = 0;
		$limit = 20;
		while(!empty($last) && $last['pid'] == 0) {
			$arrlist = $this->index_fetch(array('uid'=>$uid, 'isimage'=>0), array('aid'=>-1), $start, $limit);
			if(empty($arrlist)) break; //  || count($imagelist)
			($last = array_pop($arrlist)) && array_push($arrlist, $last);
			$attachlist += $arrlist;
			($last = array_pop($attachlist)) && array_push($attachlist, $last);
			$start += $limit;
		}
		if($filter) {
			foreach($attachlist as $k=>$attach) {
				if(!isset($attach['pid']) || $attach['pid'] > 0) {
					unset($attachlist[$k]);
				}
			}
		}
		return $attachlist;
	}
	
	// 清理未关联的垃圾
	public function gc() {
		$attachlist = $this->index_fetch(array('fid'=>0), array(), 0, 2000);
		foreach($attachlist as $v) {
			$this->unlink($v);
			$this->delete($v['fid'], $v['aid']);
		}
	}
	
	// hook attach_model_end.php
}
?>