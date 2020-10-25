<?php

class BannerAction extends GlobalAction{
	
	public function getList(){
		
		$banner = C('HD_banner');
		
		echo json_encode(array('list' => $banner, 'success' => true));
		
	}
	
	public function add(){
	
		$flag = true;
	
		$data = I('post.');
		
		foreach($data as $key => $val){
		
			$data[$key] = despiteStr($val);
		
		}		
	
		$banner = C('HD_banner');
		
		$file = C('HDCWS_DIR') . 'Config/config.banner.php';
		
		$id = md5(time() . rand(1, 100));
		
		$banner[] = array(
				
			'id' => $id,
				
			'title' => $data['title'],
				
			'src' => $data['src'],
				
			'url' => $data['url'],
				
			'target' => $data['target'],
				
			'sort' => $data['sort']
		
		);
		
		$banner = $this -> sortBanner($banner);

		$text = "<?php return array('HD_banner' => " . var_export($banner, true) . ");";
		
		if(false !== fopen($file, 'w+')){
			
			if(!file_put_contents($file, $text)) $flag = false;
			
		}else{
			
			$flag = false;
			
		}
	
		echo json_encode(array('success' => $flag));
	
	}	
	
	public function edit(){

		$flag = true;
	
		$data = I('post.');
		
		foreach($data as $key => $val){
		
			$data[$key] = despiteStr($val);
		
		}
	
		$banner = C('HD_banner');
		
		$file = C('HDCWS_DIR') . 'Config/config.banner.php';
		
		foreach($banner as $key => $value){

			if($value['id'] == $data['id']){

				$value = array(
				
					'id' => $data['id'],
			
					'title' => $data['title'],
			
					'src' => $data['src'],
			
					'url' => $data['url'],
			
					'target' => $data['target'],
			
					'sort' => $data['sort']
				
				);
				
			}
			
			$banner[$key] = $value;
			
		}
		
		$banner = $this -> sortBanner($banner);

		$text = "<?php return array('HD_banner' => " . var_export($banner, true) . ");";
		
		if(false !== fopen($file, 'w+')){
			
			if(!file_put_contents($file, $text)) $flag = false;
			
		}else{
			
			$flag = false;
			
		}
	
		echo json_encode(array('success' => $flag));

	}
	
	protected function sortBanner($arr){

		if(!empty($arr)){
			
			for($i = 0; $i < count($arr) - 1; $i++){
			
				for($j = $i + 1; $j < count($arr); $j++){
			
					if($arr[$i]['sort'] < $arr[$j]['sort']){
						
						$temp = $arr[$i];
						
						$arr[$i] = $arr[$j];
						
						$arr[$j] = $temp;
						
					}

				}

			}

		}
		
		return $arr;

	}
	
	public function del(){
	
		$flag = true;
	
		$id = $_POST['id'];
		
		$banner = C('HD_banner');
		
		$newBanner = array();
		
		$file = C('HDCWS_DIR') . 'Config/config.banner.php';
		
		foreach($banner as $key => $value){
		
			if($value['id'] != $id){
		
				array_push($newBanner, $value);
		
			}
				
		}
		
		$text = "<?php return array('HD_banner' => " . var_export($newBanner, true) . ");";
		
		if(false !== fopen($file, 'w+')){
				
			if(!file_put_contents($file, $text)) $flag = false;
				
		}else{
				
			$flag = false;
				
		}
		
		echo $flag ? 1 : 0;
	
	}
		
	//上传图片
	public function upload(){

		$uploadDir = C('UPLOAD_Banner_Dir');
		
		if(!empty($_FILES)){
		
			$fileKey = 'img';
				
			$imgPath = rtrim($uploadDir, '/') . '/' . date('Y') . '/' . date('m');
		
			$targetFile = C('HDCWS_DIR') . $imgPath;
		
			$this -> mkdirs($targetFile);
				
			$tempFile = $_FILES[$fileKey]['tmp_name'];
		
			$fileTypes = '*.jpg;*.jpeg;*.png;*.bmp;*.gif';
		
			$fileParts = pathinfo($_FILES[$fileKey]['name']);
		
			$extension = $fileParts['extension'];
				
			$imgName = md5($_FILES[$fileKey]['name'] . time()) . '.' . $extension;
		
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
	
}