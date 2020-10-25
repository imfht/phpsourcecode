<?php

class SystemAction extends GlobalAction{

	public function getDeskTopBgList(){

		$uploadDir = C('UPLOAD_Desktop_Dir');

		$list = $this -> getFiles($uploadDir);
		
		echo json_encode(array('children' => $list, 'success' => true));

	}

	protected function getFiles($dir){

		$files = array();

		if (is_dir($dir)){

			if ($dh = opendir($dir)){

				while (($file = readdir($dh))!= false){

					if($file != '.' && $file != '..') array_push($files, array('text' => $file, 'dir' => $dir . '/' . $file, 'leaf' => true));

				}

				closedir($dh);

			}

		}
		
		return $files;
	
	}

	public function saveDeskTop(){

		$result = 0;

		$url = $_REQUEST['dir'];

		$file = './Conf/config.system.php';

		if(file_exists($url)){

			$text = "<?php return array('HD_Desktop_Bg' => '" . $url . "');";

			if(false !== fopen($file, 'w+')){
			
				if(file_put_contents($file, $text)) $result = 1;
			
			}

		}
		
		echo $result;
		
		exit;
	
	}
	
	//上传背景图
	public function uploadDeskTop(){

		$uploadDir = C('UPLOAD_Desktop_Dir');
		
		if(!empty($_FILES)){
		
			$fileKey = 'desktop';

			$imgPath = rtrim($uploadDir, '/');
		
			$targetFile = C('HDCWS_DIR') . APP_NAME . '/' . $imgPath;
		
			$this -> mkdirs($targetFile);
				
			$tempFile = $_FILES[$fileKey]['tmp_name'];
		
			$fileTypes = '*.jpg;*.jpeg;*.png;*.bmp;*.gif';
		
			$fileParts = pathinfo($_FILES[$fileKey]['name']);
		
			$extension = $fileParts['extension'];

			$imgName = date('YmdHis') . '.' . $extension;
		
			$targetFile .= '/' . $imgName;

			if(preg_match("/\.\*/", $fileTypes) || preg_match("/\." . $extension . "/i", $fileTypes)){
		
				move_uploaded_file($tempFile, $targetFile);
		
					echo $imgPath . '/' . $imgName;
						
					exit;
		
			}else{
		
				echo 0;
		
			}
		
		}else{
		
			echo 0;
		
		}
		
		echo 0;
	
	}

	//删除图片
	public function delImg(){

		$result = 0;
		
		$curBg = C('HD_Desktop_Bg');
		
		$url = $_REQUEST['url'];
		
		if($curBg != $url){

			if(file_exists($url)){
			
				if(unlink($url)) $result = 1;
			
			}else $result = 1;
		
		}

		echo $result;

		exit;

	}	

}