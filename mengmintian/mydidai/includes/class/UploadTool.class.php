<?php

class UploadTool{

	static public function up($file){
		if(!isset($_FILES[$file]) && $_FILES[$file] != ''){
			return false;
		}
		if($_FILES[$file]['error'] != 0){
			return false;
		}

		if (!in_array($_FILES[$file]['type'],array('image/png','image/gif','image/jpeg','image/jpg'))){
			exit('上传文件格式不支持！');
		}

		if(is_dir(ROOT_PATH.'uploads/'.date('Ymd')) || mkdir(ROOT_PATH.'uploads/'.date('Ymd'),0777,true)){
            $file_url = 'uploads/'.date('Ymd').'/'.time().uniqid().'.'.substr(stristr($_FILES[$file]['type'],'/'),1);
			if(!move_uploaded_file($_FILES[$file]['tmp_name'],ROOT_PATH.$file_url)){
				exit('文件移动失败！');
			}
		}else{
			exit('目录创建失败！');
		}
		echo $file_url;
		return $file_url;
	}


}



?>