<?php
//版权所有(C) 2014 www.ilinei.com

namespace album\control;

use admin\model\_log;
use album\model\_album;
use album\model\_album_photo;
use ilinei\upload;
use ilinei\image;

//直接访问，报错！
if(!defined('INIT')) exit('Access Denied');

//引入语言包
require_once ROOTPATH.'/module/album/lang.php';

//相册
class index{
	//默认
	public function index(){
		global $_var;
		
		$_log = new _log();
		$_album = new _album();
		$_album_photo = new _album_photo();
		
		$search = $_album->search();
		
		if($_var['gp_do'] == 'delete'){
			$album = $_album->get_by_id($_var['gp_id']);
			if($album){
				$_album->delete($album['ALBUMID']);
				
				$_log->insert($GLOBALS['lang']['album.index.log.delete']."({$album[TITLE]})", $GLOBALS['lang']['album.index']);
			}
		}
		
		if($_var['gp_do'] == 'delete_list' && is_array($_var['gp_cbxItem'])){
			$album_titles = '';
			
			foreach($_var['gp_cbxItem'] as $key => $val){
				$album = $_album->get_by_id($val);
				if($album){
					$_album->delete($album['ALBUMID']);
					
					$album_titles .= $album['NAME'].', ';
				}
				
				unset($album);
			}
			
			if($album_titles) $_log->insert($GLOBALS['lang']['album.index.log.delete.list']."({$album_titles})", $GLOBALS['lang']['album.index']);
		}
		
		$count = $_album->get_count($search['wheresql']);
		if($count){
			$perpage = $_var['psize'];
			$pages = @ceil($count / $perpage);
			$_var['page'] = $_var['page'] > $pages ? $pages : $_var['page'];
			$start = ($_var['page'] - 1) * $perpage;
			
			$albums = $_album->get_list($start, $perpage, $search['wheresql']);
			
			$pager = pager($count, $perpage, $_var['page'], "{ADMIN_SCRIPT}/album{$search[querystring]}", $perpage);
		}
		
		include_once view('/module/album/view/index');
	}
	
	//添加
	public function _pub(){
		global $_var;
		
		$_log = new _log();
		$_album = new _album();
		$_album_photo = new _album_photo();
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['album.index.edit.validate.title']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtIdentity'] = utf8substr($_var['gp_txtIdentity'], 0, 30);
				$_var['gp_txtRemark'] = utf8substr($_var['gp_txtRemark'], 0, 100);
				
				$albumid = $_album->insert(array(
				'TITLE' => $_var['gp_txtTitle'], 
				'IDENTITY' => $_var['gp_txtIdentity'], 
				'REMARK' => $_var['gp_txtRemark'], 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s')
				));
				
				$focus_filepath = '';
				
				foreach($_var['gp_hdnNewPhotoPath'] as $key => $filepath){
					if(!$filepath) continue;
					$filepath = substr($filepath, 7);
					$temparr = explode('|', $filepath);
					$temparr = get_file_stat(format_file_path($temparr[0]));
					
					$_album_photo->insert(array(
					'TITLE' => $_var['gp_txtNewPhotoTitle'][$key], 
					'REMARK' => $_var['gp_txtNewPhotoRemark'][$key], 
					'SIZE' => $temparr['size'], 
					'INFO' => serialize($temparr), 
					'FILE01' => $filepath, 
					'USERID' => $_var['current']['USERID'],
					'USERNAME' => $_var['current']['USERNAME'], 
					'EDITTIME' => date('Y-m-d H:i:s'), 
					'ALBUMID' => $albumid, 
					'DISPLAYORDER' => $_var['gp_txtNewPhotoDisplayOrder'][$key] + 0
					));
					
					if($_var['gp_hdnNewPhotoFocus'][$key]) $focus_filepath = $filepath;
					
					unset($temparr);
				}
				
				$album_stat = $_album_photo->get_stat($albumid);
				
				$_album->update($albumid, array(
				'FILE01' => $focus_filepath, 
				'SIZE' => $album_stat['SIZE'], 
				'PHOTOS' => $album_stat['PHOTOS'], 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s')
				));
				
				$_log->insert($GLOBALS['lang']['album.index.log.add']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['album.index']);
				
				show_message($GLOBALS['lang']['album.index.message.add'], "{ADMIN_SCRIPT}/album");
			}
		}
		
		include_once view('/module/album/view/index_edit');
	}
	
	//修改
	public function _update(){
		global $_var;
		
		$_log = new _log();
		$_album = new _album();
		$_album_photo = new _album_photo();

        $search = $_album->search();

		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/album");
		
		$album = $_album->get_by_id($id);
		if($album == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/album"); 
		
		$photo_list = $_album_photo->get_all($id);
		foreach($photo_list as $key => $photo){
			$photo_list[$key]['FOCUS'] = 0;
			if($photo['FILE01'][4] == $album['FILE01']) $photo_list[$key]['FOCUS'] = 1;
		}
		
		if($_var['gp_formsubmit']){
			$_var['msg'] = '';
			
			if(empty($_var['gp_txtTitle'])) $_var['msg'] .= $GLOBALS['lang']['album.index.edit.validate.title']."<br/>";
			
			if(empty($_var['msg'])){
				$_var['gp_txtTitle'] = utf8substr($_var['gp_txtTitle'], 0, 50);
				$_var['gp_txtIdentity'] = utf8substr($_var['gp_txtIdentity'], 0, 30);
				$_var['gp_txtRemark'] = utf8substr($_var['gp_txtRemark'], 0, 100);
				
				$focus_filepath = '';
				
				$_album_photo->update_batch("ALBUMID = '{$album[ALBUMID]}'", array('ALBUMID' => 0));
				
				foreach($_var['gp_hdnPhotoPath'] as $key => $filepath){
					$temparr = explode('|', $filepath);
					$temparr = get_file_stat(format_file_path($temparr[0]));
					
					$_album_photo->update($key, array(
					'TITLE' => $_var['gp_txtPhotoTitle'][$key], 
					'REMARK' => $_var['gp_txtPhotoRemark'][$key], 
					'DISPLAYORDER' => $_var['gp_txtPhotoDisplayOrder'][$key] + 0, 
					'SIZE' => $temparr['size'], 
					'USERID' => $_var['current']['USERID'],
					'USERNAME' => $_var['current']['USERNAME'], 
					'EDITTIME' => date('Y-m-d H:i:s'), 
					'ALBUMID' => $album['ALBUMID'], 
					));
					
					if($_var['gp_hdnPhotoFocus'][$key]) $focus_filepath = $filepath;
					
					unset($temparr);
				}
				
				foreach($_var['gp_hdnNewPhotoPath'] as $key => $filepath){
					if(!$filepath) continue;
					
					$filepath = substr($filepath, 7);
					$temparr = explode('|', $filepath);
					$temparr = get_file_stat(format_file_path($temparr[0]));
					
					$_album_photo->insert(array(
					'TITLE' => $_var['gp_txtNewPhotoTitle'][$key], 
					'REMARK' => $_var['gp_txtNewPhotoRemark'][$key], 
					'SIZE' => $temparr['size'], 
					'INFO' => serialize($temparr), 
					'FILE01' => $filepath, 
					'USERID' => $_var['current']['USERID'],
					'USERNAME' => $_var['current']['USERNAME'], 
					'EDITTIME' => date('Y-m-d H:i:s'), 
					'ALBUMID' => $album['ALBUMID'], 
					'DISPLAYORDER' => $_var['gp_txtNewPhotoDisplayOrder'][$key] + 0
					));
					
					if($_var['gp_hdnNewPhotoFocus'][$key]) $focus_filepath = $filepath;
					
					unset($temparr);
				}
				
				$album_stat = $_album_photo->get_stat($album['ALBUMID']);
				
				$_album->update($album['ALBUMID'], array(
				'TITLE' => $_var['gp_txtTitle'], 
				'IDENTITY' => $_var['gp_txtIdentity'], 
				'REMARK' => $_var['gp_txtRemark'], 
				'FILE01' => $focus_filepath, 
				'SIZE' => $album_stat['SIZE'], 
				'PHOTOS' => $album_stat['PHOTOS'], 
				'USERID' => $_var['current']['USERID'],
				'USERNAME' => $_var['current']['USERNAME'], 
				'EDITTIME' => date('Y-m-d H:i:s')
				));
				
				$delete_photos = $_album_photo->get_all(0);
				foreach($delete_photos as $key => $val){
					file_clear($val, 1);
				}
				
				$_album_photo->delete_batch("ALBUMID = 0");
				
				$_log->insert($GLOBALS['lang']['album.index.log.update']."({$_var[gp_txtTitle]})", $GLOBALS['lang']['album.index']);
				
				show_message($GLOBALS['lang']['album.index.message.update'], "{ADMIN_SCRIPT}/album&page={$_var[page]}&psize={$_var[psize]}{$search[querystring]}");
			}
		}
		
		include_once view('/module/album/view/index_edit');
	}
	
	//查看
	public function _view(){
		global $_var;
		
		$_album = new _album();
		$_album_photo = new _album_photo();
		
		$id = $_var['gp_id'] + 0;
		if($id == 0) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/album");
		
		$album = $_album->get_by_id($id);
		if($album == null) show_message($GLOBALS['lang']['error'], "{ADMIN_SCRIPT}/album");
		
		$photo_list = $_album_photo->get_all($id);
		foreach($photo_list as $key => $photo){
			$photo_list[$key]['FOCUS'] = 0;
			if($photo['FILE01'][4] == $album['FILE01']) $photo_list[$key]['FOCUS'] = 1;
		}
		
		include_once view('/module/album/view/index_view');
	}
	
	//上传图片
	public function _upload(){
		global $_var;
		
		if(!$_var['current']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.login']);
	
		if($_FILES['Filedata']['name']){
			$upload = new upload();
			$cimage = new image();
			
			$upload->init($_FILES['Filedata'], 'mutual');
			
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.error']);
			if(!$upload->attach['isimage']) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.image']);
			
			$upload->save();
			
			if($upload->error()) exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.move']);
			
			if($upload->attach){
				$temp_img_size = getimagesize('attachment/'.$upload->attach['target']);
				$thumb = thumb_image($cimage, $upload->attach['target'], array('ImageWidth' => 480, 'ImageHeight' => 270, 'ThumbType' => 1));
				
				exit_echo('FILEID:'.$upload->attach['target'].'|'.$upload->attach['name'].'|'.$thumb.'|'.$temp_img_size[0].'|'.$temp_img_size[1]);
			}
		}
		
		exit_echo($GLOBALS['lang']['admin.validate.swfupload.echo.fail']);
	}
	
}
?>