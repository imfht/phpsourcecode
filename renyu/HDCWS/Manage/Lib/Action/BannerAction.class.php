<?php

class BannerAction extends GlobalAction{

	public function index(){
		
		$data = C('HD_banner');
		
		$this -> assign('list', $data);
	
		$this -> display();
	
	}
	
	public function add(){

		$this -> display();
	
	}
	
	public function adddata(){
	
		$flag = true;
		
		$data = I('post.');
		
		if(empty($data['title'])){
		
			$this -> error('标题不能为空');
			
		}
		
		if(empty($data['url'])) $this -> error('链接不能为空');
		
		if(!preg_match('/^\d+$/', $data['sort'])) $this -> error('排序须是正整数');
	
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
		
		if($flag){
		
			$this -> success('添加成功', U('Banner/index'));	
				
		}else $this -> error('添加失败');
	
	}
	
	public function edit(){
	
		$id = $_GET['id'];
		
		$list = C('HD_banner');

		foreach($list as $k => $b){

			if($b['id'] == $id){
			
				$data = $b;
				
				break;
			
			}
		
		}

		if(empty($data)) $this -> error('Banner不存在');
		
		else{
		
			$this -> assign('data', $data);
			
			$this -> display();
		
		}
	
	}
	
	public function editdata(){

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
	
		if($flag){
		
			$this -> success('编辑成功', U('Banner/index'));	
				
		}else $this -> error('编辑失败');

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
	
		$id = $_GET['id'];
		
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

		if($flag){

			$this -> success('删除成功', U('Banner/index'));	

		}else $this -> error('删除失败');
	
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