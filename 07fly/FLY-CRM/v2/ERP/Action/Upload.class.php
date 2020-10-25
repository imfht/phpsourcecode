<?php 
class Upload extends Action{	

	private $cacheDir='';//缓存目录
	private $fp    ='';//缓存目录
	
	public function __construct() {
		$this->fp=_instance('Extend/File');
	}
	
	//图片上传地址
	public function upload_images_path(){
		$date =date("ymd",time());
		$path = "upload/images/$date/";
		$this->fp->create_dir($path);
		return $path;
	}		
	
	//编辑框架上传图片
	public function upload_images_editor(){
		$files  = $_FILES["filedata"];
		if(empty($_FILES)){
			$msg=array('err'=>'错误信息提示','msg'=>'http://www.07fly.com/');
			echo json_encode($msg);
		}else{
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
				'err'=>'',
				'msg'=>'/'.$pic_path
			);
			//echo '{err:"",msg:"/Upload/image.jpg"}';
			echo json_encode($arr);
		}
	}
	
	//百度上传控件
	public function upload_images_webuploader(){
		$files  = $_FILES["file"];
		$picname = $files['name'];
		$picsize = $files['size'];
		$pictype = $files['type'];
		if ($picsize > 1024000000000) {
			echo '图片大小不能超过1M';
			exit;
		}
		$pic_type = strstr($picname, '.');
		$pic_name = date("YmdHis").rand(100, 999).$pic_type;
		
		$pic_path = $this->upload_images_path();//上传路径
		$pic_path = $pic_path.$pic_name;//上传文件路径+文件名
		move_uploaded_file($files['tmp_name'], $pic_path);
		$res = array('success'=>true,'file'=> '/'.$pic_path);
		die(json_encode($res));
	}
	
	//ifname传传框架
	public function upload_img(){
		if(empty($_POST)){
			$imgs_id= $this->_REQUEST('imgs_id');
			$smarty = $this->setSmarty();
			$smarty->assign(array("imgs_id"=>$imgs_id));
			$smarty->display('upload.html');			
		}else{	
		}
	}
	//删除上传图片，
	//@imgfile = 图片绝对路径
	public function upload_img_remove(){
		$imgfile=".".$this->_REQUEST('imgfile');
		if($this->fp->unlink_file($imgfile)){
			$rtn=array('rtnstatus'=>'success','message'=>'删除成功');	
		}else{
			$rtn=array('rtnstatus'=>'fail','message'=>'删除失败');	
		}
		echo json_encode($rtn);
	}	
	
	
	public function upload_img_save(){
		if(empty($_FILES)){
			$smarty = $this->setSmarty();
			$smarty->display('upload/iframe.html');			
		}else{	
			$files  = $_FILES["filename"];
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
				'spath'=>"/".str_replace(CACHE,"",$pic_path),
				'simg'=>"<img src=/".str_replace(CACHE,"",$pic_path).">",
			);
			echo json_encode($arr);	
		 }	
	}

	//多图片上传控件
	public function upload_images_ismove(){
		//允许上传文件格式
		$typeArr = array("jpg", "png", "gif", "jpeg");
		//上传路径
		$path = $this->upload_images_path();
		
		$name = $_FILES['file_upload']['name'];
		$size = $_FILES['file_upload']['size'];
		$name_tmp = $_FILES['file_upload']['tmp_name'];
		if (empty($name)) {
			echo json_encode(array("error" => "您还未选择图片"));
			exit;
		}
		//获取文件类型
		$type = strtolower(substr(strrchr($name, '.'), 1));
		if (!in_array($type, $typeArr)) {
			echo json_encode(array("error" => "清上传jpg,png或gif类型的图片！"));
			exit;
		}
		//上传大小
		if ($size > 5 * 1024 * 1024) {
			echo json_encode(array("message" => "图片大小已超过5m！"));
			exit;
		}
		$time_str = time() . rand(10000, 99999);
		//图片名称
		$pic_name = $time_str . "." . $type;
		//上传后图片路径+名称
		$pic_url = $path . $pic_name;
		//临时文件转移到目标文件夹
		if (move_uploaded_file($name_tmp, $pic_url)) {
			//这些数据可根据需要进行返回，字段如果修改需要和前端保持一致
			$ret = array(
				'file_id' => $time_str,
				'file_name' => '/'.$pic_url,
				'origin_file_name' => $name,
				'file_path' => '/'.$pic_url,
				'state' => '1',
			);
			echo json_encode($ret);
		} else {
			$ret = array(
				'message' => "图片上传失败",
				'origin_file_name' => $name,
				'state' => '0',
			);
			echo json_encode($ret);
		}
	}	
	//上传一个文件接口
	public function upload_images(){
			$files  = $_FILES["filepath"];
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
				'spath'=>"/".str_replace(CACHE,"",$pic_path),
				'simg'=>"<img src=/".str_replace(CACHE,"",$pic_path).">",
			);
			echo json_encode($arr);	
	}
		
}//end class
?>