<?php

/*
 * Copyright (C) xiuno.com
 */

!defined('FRAMEWORK_PATH') && exit('FRAMEWORK_PATH not defined.');

include BBS_PATH.'control/common_control.class.php';

class ueditor_control extends common_control {
	
	function __construct(&$conf) {
		parent::__construct($conf);
		$this->_checked['bbs'] = ' class="checked"';
		$this->_title[] = $this->conf['seo_title'] ? $this->conf['seo_title'] : $this->conf['app_name'];
		$this->_seo_keywords = $this->conf['seo_keywords'];
		$this->_seo_description = $this->conf['seo_description'];
	}
	
	private function uploaderror($s) {
		echo "{'url':'','title':'','original':'','state':'" . $s . "'}";
		exit;
	}
	
	// 给插件预留个位置
	public function on_uploadimage() {
		$pid = intval(core::gpc('pid'));
		//$tid = intval(core::gpc('tid'));
		$fid = intval(core::gpc('fid'));
		$pid == 0 && $fid = 0;
		$tid = 0;
		if($pid > 0 && $fid > 0) {
			$post = $this->post->read($fid, $pid);
			$this->check_post_exists($post);
			$tid = $post['tid'];
		}
		
		log::trace(print_r($_COOKIE, 1));
		
		$uid = $this->_user['uid'];
		$this->check_forbidden_group();
		$this->check_login();
		
		$user = $this->user->read($uid);
		
		if(empty($_FILES['upfile'])) {
			$this->uploaderror('没有上传文件。');
		}
		//  && is_file($_FILES['upfile']['tmp_name'])
		if(!empty($_FILES['upfile']['tmp_name'])) {
			
			// 对付一些变态的 iis 环境， is_file() 无法检测无权限的目录。
			$tmpfile = FRAMEWORK_TMP_TMP_PATH.md5(rand(0, 1000000000).$_SERVER['time'].$_SERVER['ip']).'.tmp';
			$succeed = IN_SAE ? copy($_FILES['upfile']['tmp_name'], $tmpfile) : move_uploaded_file($_FILES['upfile']['tmp_name'], $tmpfile);
			if(!$succeed) {
				$this->uploaderror('移动临时文件错误，请检查临时目录的可写权限。');
			}
			
			$file = $_FILES['upfile'];
			$file['tmp_name'] = $tmpfile;
			core::htmlspecialchars($file['name']);
			$filetype = $this->attach->get_filetype($file['name']);
			if($filetype != 'image') {
				$allowtypes = $this->attach->get_allow_filetypes();
				$this->uploaderror('请选择图片格式的文件，后缀名为：.gif, .jpg, .png, .bmp！');
			}
			
			if(!$this->attach->is_safe_image($file['tmp_name'])) {
				$this->uploaderror('系统检测到图片('.$file['name'].')不是安全的，请更换其他图片试试。');
			}
			
			$arr = array (
				'fid'=>$fid,
				'tid'=>$tid,
				'pid'=>$pid,
				'filesize'=>0,
				'width'=>0,
				'height'=>0,
				'filename'=>'',
				'orgfilename'=>$file['name'],
				'filetype'=>$filetype,
				'dateline'=>$_SERVER['time'],
				'comment'=>'',
				'downloads'=>0,
				'isimage'=>1,
				'golds'=>0,
				'uid'=>$this->_user['uid'],
			);
			$aid = $this->attach->create($arr);
			
			$uploadpath = $this->conf['upload_path'].'attach/';
			$uploadurl = $this->conf['upload_url'].'attach/';
			
			// 处理文件
			$imginfo = getimagesize($file['tmp_name']);
			
			// 如果为 GIF, 直接 copy
			// 判断文件类型，如果为图片文件，缩略，否则直接保存。
			if($imginfo[2] == 1) {
				$md5name = md5(rand(0, 1000000000).$_SERVER['time'].$_SERVER['ip']);
				$fileurl = image::set_dir($aid, $uploadpath).'/'.$md5name.'.gif';
				$thumbfile = $uploadpath.$fileurl;
				copy($file['tmp_name'], $thumbfile);
				$r['filesize'] = filesize($file['tmp_name']);
				$r['width'] = $imginfo[0];
				$r['height'] = $imginfo[1];
				$r['fileurl'] = $fileurl;
			} else {
				$r = image::safe_thumb($file['tmp_name'], $aid, '.jpg', $uploadpath, $this->conf['upload_image_max_width'], 240000, 1);	// 1210 800
				$thumbfile = $uploadpath.image::thumb_name($r['fileurl']);
				image::clip_thumb($file['tmp_name'], $thumbfile, $this->conf['thread_icon_middle'], $this->conf['thread_icon_middle']);
			}
			
			$arr['aid'] = $aid;
			$arr['fid'] = $fid;
			$arr['filesize'] = $r['filesize'];
			$arr['width'] = $r['width'];
			$arr['height'] = $r['height'];
			$arr['filename'] = $r['fileurl'];
			$this->attach->update($arr);
			
			is_file($file['tmp_name']) && unlink($file['tmp_name']);
			
			$title = htmlspecialchars(core::gpc('pictitle', 'P'));
			echo "{'url':'" . $uploadurl.$arr['filename'] . "','title':'" . $title . "','original':'" . $file['name'] . "','state':'SUCCESS'}";
			exit;
		} else {
			if($_FILES['upfile']['error'] == 1) {
				$this->uploaderror('上传文件( '.htmlspecialchars($_FILES['upfile']['name']).' )太大，超出了 php.ini 的设置：'.ini_get('upload_max_filesize'));
			} else {
				$this->uploaderror('上传文件失败，错误编码：'.$_FILES['upfile']['error'].', FILES: '.print_r($_FILES, 1).', is_file: '.is_file($_FILES['upfile']['tmp_name']).', file_exists: '.file_exists($_FILES['upfile']['tmp_name'])); // .print_r($_FILES, 1)
			}
		}
	}
	
	// 上传附件
	public function on_attach() {
		$fid = intval(core::gpc('fid'));
		$pid = intval(core::gpc('pid'));
		$uid = $this->_user['uid'];
		
		$this->check_forbidden_group();
		$this->check_login();
		
		$attachlist = array();
		if($fid && $pid) {
			$attachlist = $this->attach->get_list_by_fid_pid($fid, $pid);
		} else {
			$attachlist = $this->attach->get_uploading_attachlist($uid);
		}
		foreach($attachlist as &$attach) {
			$this->attach->format($attach);
		}
		
		$this->init_editor_attach($attachlist);
		$this->view->assign('fid', $fid);
		$this->view->assign('pid', $pid);
		$this->view->display('plugin_baidu_editor_attach_list.htm');
		
	}
	
	private function init_editor_attach($attachlist) {
		$attachnum = count($attachlist);
		$this->view->assign('attachlist', $attachlist);
		$this->view->assign('attachnum', $attachnum);
		$upload_max_filesize = $this->attach->get_upload_max_filesize();
		$this->view->assign('upload_max_filesize', $upload_max_filesize);
		$filetyps = core::json_encode($this->attach->filetypes);
		$this->view->assign('filetyps', $filetyps);
	}
	
	// 获取远程图片，只针对斑竹以上用户组开放。
	public function on_getremoteimage() {
		$pid = intval(core::gpc('pid'));
		//$tid = intval(core::gpc('tid'));
		$fid = intval(core::gpc('fid'));
		$pid == 0 && $fid = 0;
		$tid = 0;
		if($pid > 0 && $fid > 0) {
			$post = $this->post->read($fid, $pid);
			$this->check_post_exists($post);
			$tid = $post['tid'];
		}
		
		$uid = $this->_user['uid'];
		$this->check_forbidden_group();
		$this->check_login();
		
		$url = htmlspecialchars(core::gpc( 'upfile', 'P'));
		$url = str_replace( "&amp;" , "&" , $url);
		
		$urllist = explode("ue_separate_ue", $url);
		$returnurl = array();
		foreach($urllist as $url) {
			if(empty($url)) {
				//$this->uploaderror('没有URL。');
				$returnurl[] = 'error';
				continue;
			}
			
			preg_match('#/([^/]+)\.(jpg|jpeg|png|gif|bmp)#i', $url, $m);
			if(empty($m[2])) {
				//$this->uploaderror('只支持 jpg, jpeg, png, gif, bmp 格式。');
				$returnurl[] = 'error';
				continue;
			}
			$ext = $m[2];
			$filename = $m[0];
			if(!preg_match('#^(https?://[^\'"\\\\<>:\s]+(:\d+)?)?([^\'"\\\\<>:\s]+?)*$#is', $url)) {
				//$this->uploaderror('URL 格式不正确。');
				$returnurl[] = 'error';
				continue;
			}
			
			$s = misc::fetch_url($url, 5);
				
			$tmpfile = FRAMEWORK_TMP_TMP_PATH.md5(rand(0, 1000000000).$_SERVER['time'].$_SERVER['ip']).'.'.$ext;
			$succeed = file_put_contents($tmpfile, $s);
			if(!$succeed) {
				//$this->uploaderror('移动临时文件错误，请检查临时目录的可写权限。');
				$returnurl[] = 'error';
				continue;
			}
			
			$arr = array (
				'fid'=>$fid,
				'tid'=>$tid,
				'pid'=>$pid,
				'filesize'=>filesize($tmpfile),
				'width'=>0,
				'height'=>0,
				'filename'=>'',
				'orgfilename'=>$filename,
				'filetype'=>'image',
				'dateline'=>$_SERVER['time'],
				'comment'=>'',
				'downloads'=>0,
				'isimage'=>1,
				'golds'=>0,
				'uid'=>$this->_user['uid'],
			);
			$aid = $this->attach->create($arr);
			
			$uploadpath = $this->conf['upload_path'].'attach/';
			$uploadurl = $this->conf['upload_url'].'attach/';
			
			$r = image::safe_thumb($tmpfile, $aid, '.jpg', $uploadpath, $this->conf['upload_image_max_width'], 240000, 1);	// 1210 800
			$thumbfile = $uploadpath.image::thumb_name($r['fileurl']);
			image::clip_thumb($tmpfile, $thumbfile, $this->conf['thread_icon_middle'], $this->conf['thread_icon_middle']);
			
			// 处理文件
			$imginfo = getimagesize($thumbfile);
				
			$arr['aid'] = $aid;
			$arr['width'] = $imginfo[0];
			$arr['height'] = $imginfo[1];
			$arr['filename'] = $r['fileurl'];
			$this->attach->update($arr);
			
			is_file($tmpfile) && unlink($tmpfile);
		
			$returnurl[] = $uploadurl.$arr['filename'];
		}
		echo "{'url':'" . implode('ue_separate_ue', $returnurl) . "','tip':'远程图片抓取成功！','srcUrl':'" . core::gpc( 'upfile', 'P') . "','state':'SUCCESS'}";
		exit;
	}
	
}

?>