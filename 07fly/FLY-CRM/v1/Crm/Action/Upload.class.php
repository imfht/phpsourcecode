<?php 
/**
 * 上传操作
 *
 * All Manager Finance , orderinfo
 *
 * @copyright   Copyright (C) 2012-2015 07FLY Network Technology Co,LTD (www.07FLY.com)
 *				All rights reserved.
 * @license     LGPL 
 * @author      NIAOMUNIAO <1871720801@QQ.com>
 * @package     system
 * @version     1.0
 * @link        http://www.07fly.net http://www.07fly.top
 * @version   Ajax.class.php  add by NIAOMUNIAO 2013-07-27 16:48 
 */	 
class Upload extends Action{	

	private $cacheDir='';//缓存目录
	public function main(){

	}
	
	public function User(){
		return _instance('Action/User');
	}
	public function Common(){
		return _instance('Extend/Common');
	}
	public function File(){
		return _instance('Extend/File');
	}	
	
	public function upload_user_import_path(){
		$path = CACHE."/import/";
		$this->L("File")->create_dir($path);
		return $path;
	}
	public function upload_upgrade_path(){
		$path = CACHE."/upgrade/";
		$this->L("File")->create_dir($path);
		return $path;
	}	
	
	public function upload_images_path(){
		$path = CACHE."/images/";
		$this->L("File")->create_dir($path);
		return $path;
	}		
	
	public function user_import_upload(){
		$picname = $_FILES['mypic']['name'];
		$picsize = $_FILES['mypic']['size'];
		if ($picname != "") {
			$type = strstr($picname, '.');
			$rand = rand(100, 999);
			$pics = date("YmdHis") . $rand . $type;
			//上传路径
			$pic_path = $this->upload_user_import_path();
			$pic_path = $pic_path.$pics;
			move_uploaded_file($_FILES['mypic']['tmp_name'], $pic_path);
		}
		$size = round($picsize/1024,2);
		$arr = array(
			'name'=>$picname,
			'pic'=>$pics,
			'size'=>$size
		);
		echo json_encode($arr);		
	}
	
	public function user_import_del(){
		$dirname  = $this->upload_user_import_path();
		$filename = ($_GET["filename"])?$_GET["filename"]:$_POST["filename"];
		if($this->File()->unlink_file($dirname.$filename)){
			echo "1";
		}else{
			echo "删除失败";
		}
	}
    

	
	public function sys_upgrade_upload(){
		$picname = $_FILES['mypic']['name'];
		$picsize = $_FILES['mypic']['size'];
		if ($picname != "") {
			$type = strstr($picname, '.');
			$rand = rand(100, 999);
			$pics = date("YmdHis") . $rand . $type;
			//上传路径
			$pic_path = $this->upload_upgrade_path();
			$pic_path = $pic_path.$pics;
			move_uploaded_file($_FILES['mypic']['tmp_name'], $pic_path);
		}
		$size = round($picsize/1024,2);
		$arr = array(
			'name'=>$picname,
			'pic'=>$pics,
			'size'=>$size
		);
		echo json_encode($arr);		
	}

	public function upload_img(){
		if(empty($_POST)){
			$smarty = $this->setSmarty();
			$smarty->display('upload/upload.html');			
		}else{	
		}
		
	}
	
	public function upload_img_save(){
		if(empty($_FILES)){
			$smarty = $this->setSmarty();
			$smarty->display('upload/iframe.html');			
		}else{	
			$files    = $_FILES["filename"];
			$picname = $files['name'];
			$picsize = $files['size'];
			$pictype = $files['type'];
			if ($picname != "") {
				if ($picsize > 1024000000000) {
					echo '图片大小不能超过1M';
					exit;
				}
				$type = strstr($picname, '.');
				$rand = rand(100, 999);
				$pics = date("YmdHis") . $rand . $type;
				//上传路径
				$pic_path = $this->upload_images_path();
				$pic_path = $pic_path.$pics;
				move_uploaded_file($files['tmp_name'], $pic_path);
			}
			$size = round($picsize/1024,2);
			$arr = array(
				'name'=>$picname,
				'pic'=>$pics,
				'size'=>$size,
				'path'=>str_replace(ROOT,APP_HTTP,$pic_path),
				'spath'=>str_replace(CACHE,"",$pic_path),
			);
			echo json_encode($arr);	
		 }	
	}

	
	public function sys_upgrade_del(){
		$dirname  = $this->upload_upgrade_path();
		$filename = ($_GET["filename"])?$_GET["filename"]:$_POST["filename"];
		if($this->File()->unlink_file($dirname.$filename)){
			echo "1";
		}else{
			echo "删除失败";
		}
	}	
	


		
}//end class
?>