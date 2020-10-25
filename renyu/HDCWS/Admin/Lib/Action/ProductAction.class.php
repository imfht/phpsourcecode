<?php

//产品类
class ProductAction extends GlobalAction {
	
	//产品列表
	public function getList(){
		
		$start = intval($_GET['start']);
		
		$limit = intval($_GET['limit']);
		
		$key = $_GET['key'];
		
		$cid = intval($_GET['cid']);
		
		$cidStr = empty($cid) ? '' : ' and p.cid = ' . $cid;
		
		$condition = ' where (p.name like "%' . $key . '%" or p.keywords like "%' . $key . '%" or p.description like "%' . $key . '%")' . $cidStr . ' ';		
		
		if(empty($start)) $start = 0;
		
		if(empty($limit)) $limit = 20;
		
		$pro = D('product');
		
		$count = $pro -> query('select count(*) counts from ' . C('DB_PREFIX') . 'product p ' . $condition);

		$count = empty($count) ? 0 : $count[0]['counts'];
		
		$sql = 'select p.*,c.name cname from ' . C('DB_PREFIX') . 'product p left join ' . C('DB_PREFIX') . 'product_cat c on p.cid = c.id ' . $condition . ' order by time desc limit '. $start . ',' . $limit;

		$list = $pro -> query($sql);
		
		echo json_encode(array('list' => $list, 'total' => $count, 'success' => true));
		
	}
	
	//产品添加
	public function add(){

		$flag = true;
		
		$data = I('post.');
		
		$data['content'] = I('content', '', '');
		
		foreach($data as $key => $val){
		
			$data[$key] = despiteStr($val);
		
		}
		
		$product = D('product');
		
		$data['time'] = date('Y-m-d H:i:s');
		
		$result = $product -> add($data);
		
		if(empty($result)) $flag = false;
		
		echo json_encode(array('success' => $flag));
	
	}	
	
	//产品更新
	public function edit(){
		
		$flag = true;

		$data = I('post.');
		
		$data['content'] = I('content', '', '');
		
		foreach($data as $key => $val){
		
			$data[$key] = despiteStr($val);
		
		}

		$product = D('product');

		$result = $product -> save($data);
		
		if(empty($result)) $flag = false;
		
		echo json_encode(array('success' => $flag));
		
	}
	
	//产品删除
	public function del(){
		
		$id = $_POST['id'];
		
		$result = 1;
		
		$prefix = C('DB_PREFIX');
		
		if(preg_match('/\d(\,\d)*/', $id)){

			$pro = D('product');
			
			$result = $pro -> delete($id);
			
			$result = $result > 0 ? 1 : 0;
			
		}else $result = 0;

		echo $result;

	
	}
	
	//产品类型列表
	public function getCatList(){

		$status = $_GET['status'];	

		$cat = D('product_cat');

		if($status == 'all'){
			
			$list = $cat -> order('sort desc') -> select();
			
		}else if($status === 0){

			$list = $cat -> where('status=0') -> order('sort desc') -> select();
			
		}else{

			$list = $cat -> where('status=1') -> order('sort desc') -> select();
			
		}

		echo json_encode(array('list' => $list));
	
	}
	
	//类型列表树
	public function getCatListTree(){
		
		$cat = D('product_cat');

		//$rootId = intval($_GET['id']);

		//if(empty($rootId)) $rootId = 0;

		//$list = $cat -> where('cid = ' . $rootId) -> select();
		
		$rootId = 0;
		
		$list = $cat-> order('sort desc') -> select();
		
		$listTree = $this -> listToTree($list, 'id', 'cid', 'children', $rootId);
		
		$listTree = $this -> prepaire($listTree);

		echo json_encode(array('children' => $listTree, 'success' => true));
	
	}
	
	public function addCat(){
		
		$flag = true;
		
		$data = I('post.');
		
		foreach($data as $key => $val){
		
			$data[$key] = despiteStr($val);
		
		}
		
		$cat = D('product_cat');
		
		$result = $cat -> add($data);

		if(empty($result)) $flag = false;
		
		echo json_encode(array('success' => $flag, 'id' => $result));
		
	}
	
	public function editCat(){
	
		$flag = true;
		
		$data = I('post.');
		
		foreach($data as $key => $val){
		
			$data[$key] = despiteStr($val);
		
		}
		
		$cat = D('product_cat');
		
		$result = $cat -> save($data);

		if(empty($result)) $flag = false;
		
		echo json_encode(array('success' => $flag));
	
	
	}

	public function delCat(){
	
		$flag = true;
		
		$id = intval($_POST['id']);
		
		$cat = D('product_cat');
		
		$product = D('product');
		
		$subCids = $cat -> where(array('cid' => $id)) -> count();
		
		$pros = $product -> where(array('cid' => $id)) -> find();

		if(empty($id) || !empty($pros) || $subCids) $flag = false;
		
		else{
			
			$result = $cat -> delete($id);
			
			if(empty($result)) $flag = false;
			
		}
		
		echo json_encode(array('success' => $flag));
	
	}
	
	//上传大图
	public function uploadImg(){
		
		$uploadDir = C('UPLOAD_Product_Dir');
		
		if(!empty($_FILES)){
		
			$fileKey = 'img';
				
			$imgPath = rtrim($uploadDir, '/') . '/' . date('Y') . '/' . date('m') . '/' . date('d');
		
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
	
	//上传缩略图
	public function uploadThumbImg(){
		
		$uploadDir = C('UPLOAD_Product_Dir');

		if(!empty($_FILES)){
		
			$fileKey = 'thumbImg';
			
			$imgPath = rtrim($uploadDir, '/') . '/' . date('Y') . '/' . date('m') . '/' . date('d');
		
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

				if(imageResize($targetFile, 285, 200)){

					echo $imgPath . '/' . $imgName;
					
					exit;
				
				}else{
					
					unlink($targetFile);
					
					echo 0;
				
					exit;
				
				}
		
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
		
		$url = C('HDCWS_DIR') . $_REQUEST['imgUrl'];

		if(file_exists($url)){
		
			if(unlink($url)) $result = 1;
		
		}else $result = 1;
		
		echo $result;
		
		exit;
		
	}

	public function uploadJson(){
		
		$url = C('UPLOAD_Product_Dir') . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
		
		$save_url = __ROOT__ . '/' . $url;
		
		$save_path = C('HDCWS_DIR') . $url;
	
		upload_json($save_path, $save_url);
	
	}
	
	public function fileManagerJson(){
		
		$url = C('UPLOAD_Product_Dir') . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
		
		$save_url = __ROOT__ . '/' . $url;
		
		$save_path = C('HDCWS_DIR') . $url;		
	
		file_manager_json($save_path, $save_url);
	
	}	
	
}