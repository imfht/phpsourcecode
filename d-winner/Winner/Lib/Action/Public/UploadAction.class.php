<?php
/*
 * @varsion		EasyWork系统 1.0var
 * @package		程序设计深圳市九五时代科技有限公司设计开发
 * @copyright	Copyright (c) 2010 - 2015, 95era, Inc.
 * @link		http://www.d-winner.com
 */
class UploadAction extends Action {
	//头像图片上传处理控件
    public function pic($act=NULL,$uid=NULL){
		$Public = A('Index','Public');
		
		//main
		import('ORG.Net.FileSystem');
		$sys = new FileSystem();
		import('ORG.Net.UploadFile');
		$up = new UploadFile();
		$up->allowTypes = array('image/pjpeg','image/jpeg','image/x-png','image/png','image/gif');
		$upload = C('TMPL_PARSE_STRING.__UPLOAD__');
		$up->savePath = ROOT.'/'.$upload.'/Face/';
		$up->maxSize = C('UPLOAD_SIZE');
		$up->charset = 'UTF-8';
		$up->autoSub = true;
		
		if($act!=NULL){
			$act = intval($act);
		}
		if($act==1){
			$role = $Public->check('Upload',array('p'));
			if($role<0){
				echo $role; exit;
			}
			
			$filename = I('hi');
			if($up->upload()){
				$info = $up->getUploadFileInfo();
				if($filename){
					$path = ROOT.'/'.$upload.'/Face/'.$filename;
					if(file_exists($path)){
						$df = $sys->delFile($path);
					}
				}
				echo $info[0]['savename'];
				unset($info);
			}else{
				echo -1;
			}
		}else{
			$this->assign('uid',$uid);
			$this->assign('uniqid',uniqid());
			$this->assign('upload',$upload);
			$this->display();
		}
		unset($Public,$sys,$upload,$up);
	}
	
	
	//Kindeditor中图片上传处理程序
	public function save($act=NULL,$mode='Kindeditor'){
		//main
		import('ORG.Net.FileSystem');//实例化文件系统类
		$sys = new FileSystem();
		import('ORG.Net.UploadFile');//实例化上传类
		$up = new UploadFile();
		$up->allowTypes = array('image/pjpeg','image/jpeg','image/x-png','image/png','image/gif');
		$upload = C('TMPL_PARSE_STRING.__UPLOAD__');
		$up->savePath = ROOT.'/'.$upload.'/';
		$up->maxSize = C('UPLOAD_SIZE');
		$up->charset = 'UTF-8';
		$up->autoSub = true;
		if($up->upload()){
			$info = $up->getUploadFileInfo();
			$upfile = M('Upload_file');
			$udata = array(
				'ModeName'=>$mode,
				'BelongFile'=>$act,
				'FileName'=>$info[0]['savename'],
				'CreateDate'=>date("Y-m-d",time())
			);
			$add = $upfile->add($udata);
			$data = array(
				'error'=>0,
				'message'=>"图片上传成功",
				'url'=>C('CFG_HOST')."/".$upload."/".$info[0]['savename']
			);
			echo json_encode($data);
			unset($info,$upfile,$data,$udata);
		}else{
			$info = $up->getErrorMsg();
			echo '{"error" : 1, "message" : "'.$info.'" }';
		}
		unset($sys,$up,$upload);
	}
}