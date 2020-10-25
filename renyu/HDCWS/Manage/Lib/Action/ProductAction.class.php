<?php

class ProductAction extends GlobalAction {

	public function index(){
		
		import('ORG.Util.Page');
		
		$cat = D('product_cat');
		
		$catList = $cat -> order('sort desc') -> select();
		
		$this -> assign('catList', $catList);		
		
		$pro = D('product');
		
		$key = $_GET['key'];
		
		$cid = intval($_GET['cid']);
		
		$cidStr = empty($cid) ? '' : ' and p.cid = ' . $cid;
		
		$condition = ' where (p.name like "%' . $key . '%" or p.keywords like "%' . $key . '%" or p.description like "%' . $key . '%")' . $cidStr . ' ';		
				
		$count = $pro -> query('select count(*) counts from ' . C('DB_PREFIX') . 'product p ' . $condition);

		$count = empty($count) ? 0 : $count[0]['counts'];

		$Page = new Page($count, 20);
		
		$show = $Page -> show();

		$sql = 'select p.*,c.name cname from ' . C('DB_PREFIX') . 'product p left join ' . C('DB_PREFIX') . 'product_cat c on p.cid = c.id ' . $condition . ' order by time desc limit '. $Page -> firstRow . ',' . $Page -> listRows;
		
		$list = $pro -> query($sql);
		
		$this -> assign('list', $list);
		
		$this -> assign('pageLink', $show);
		
		$this -> display();
	
	}
	
	public function add(){
		
		$cat = D('product_cat');
		
		$catList = $cat -> order('sort desc') -> select();
		
		$this -> assign('catList', $catList);

		$this -> display();
	
	}
	
	public function addproduct(){
	
		$data = I('post.');
	
		if(empty($data['name'])){
				
			$this -> error('产品名称不能为空');
				
		}
		
		if(empty($data['price']) || !preg_match('/^\d+$/', $data['price'])){
		
			$this -> error('产品价格不正确');
		
		}		
	
		if(empty($data['keywords'])) $this -> error('关键字不能为空');
	
		if(empty($data['description'])) $this -> error('简单描述不能为空');
	
		if($data['cid'] == '') $this -> error('类型不能为空');
	
		if(empty($data['content'])) $this -> error('详细描述不能为空');
	
		if(empty($data['thumburl'])){
				
			$this -> error('请上传缩略图');
				
		}
		
		if(empty($data['imgurl'])){
		
			$this -> error('请上传产品大图');
		
		}		
	
		$data['content'] = I('content', '', '');
		
		foreach($data as $key => $val){
		
			$data[$key] = stopDefaultTag($val);
		
		}		
	
		$product = D('product');
	
		$data['time'] = date('Y-m-d H:i:s');
	
		$result = $product -> add($data);
	
		if(empty($result)){
	
			$this -> error('添加失败');
				
		}else $this -> success('添加成功', U('Product/index'));
		
	}
	
	public function edit(){
		
		$id = intval($_GET['id']);
		
		$cat = D('product_cat');
		
		$catList = $cat -> order('sort desc') -> select();
		
		$this -> assign('catList', $catList);
		
		$product = D('product') -> where('id='. $id) -> find();
		
		$this -> assign('product', $product);

		$this -> display();
	
	}
	
	public function editproduct(){
	
		$data = I('post.');
		
		if(empty($data['id'])){
		
			$this -> error('产品不存在');
		
		}
	
		if(empty($data['name'])){
	
			$this -> error('产品名称不能为空');
	
		}
	
		if(empty($data['price']) || !preg_match('/^\d+$/', $data['price'])){
	
			$this -> error('产品价格不正确');
	
		}
	
		if(empty($data['keywords'])) $this -> error('关键字不能为空');
	
		if(empty($data['description'])) $this -> error('简单描述不能为空');
	
		if($data['cid'] == '') $this -> error('类型不能为空');
	
		if(empty($data['content'])) $this -> error('详细描述不能为空');
	
		if(empty($data['thumburl'])){
	
			$this -> error('请上传缩略图');
	
		}
	
		if(empty($data['imgurl'])){
	
			$this -> error('请上传产品大图');
	
		}
	
		$data['content'] = I('content', '', '');
		
		foreach($data as $key => $val){
		
			$data[$key] = stopDefaultTag($val);
		
		}
	
		$product = D('product');
	
		$result = $product -> save($data);
	
		if(empty($result)){
	
			$this -> error('修改失败');
	
		}else $this -> success('修改成功', U('Product/index'));
	
	}
	
	public function del(){
	
		$id = $_GET['id'];
	
		if(is_array($id)) $id = implode(',', $id);
	
		$result = 1;
	
		if(preg_match('/\d(\,\d)*/', $id)){
	
			$pro = D('product');
				
			$result = $pro -> delete($id);
				
			$result = $result > 0 ? 1 : 0;
				
		}else $result = 0;
	
		if($result) $this -> success('删除成功', U('Product/index'));
	
		else $this -> error('删除失败');
	
	}

	public function cat(){
		
		import('ORG.Util.Page');
		
		$cat = D('product_cat');
		
		$prefix = C('DB_PREFIX');
		
		$list = $cat-> query('select c.*,pc.name pcname from ' . $prefix . 'product_cat c left join ' . $prefix . 'product_cat pc on c.cid = pc.id order by sort desc');

		$listTree = $this -> listToTree($list, 'id', 'cid', 'children', 0);
		
		$this -> assign('list', $listTree);
		
		$count = $cat -> count();
		
		$Page = new Page($count, 20);
		
		$show = $Page -> show();
		
		$this -> assign('pageLink', $show);
		
		$this -> display();
	
	}		
	
	public function addcat(){
		
		$id = intval($_GET['id']);
		
		if(!empty($id)){
			
			$cat = D('product_cat');
				
			$catData = $cat -> where('id=' . $id) -> find();
			
			if(!empty($catData)) $this -> assign('catdata', $catData);
			
		}

		$this -> display();
	
	}
	
	public function addcatdata(){
		
		$data = I('post.');
		
		if(empty($data['name'])){
				
			$this -> error('分类名称不能为空');
				
		}
		
		if(empty($data['keywords'])) $this -> error('关键字不能为空');
		
		if(empty($data['description'])) $this -> error('简单描述不能为空');
		
		foreach($data as $key => $val){
		
			$data[$key] = stopDefaultTag($val);
		
		}
		
		$cat = D('product_cat');
		
		$result = $cat -> add($data);
		
		if(empty($result)){
		
			$this -> error('添加失败');
				
		}else $this -> success('添加成功', U('Product/cat'));
	
	}
	
	public function editcat(){
		
		$id = intval($_GET['id']);
		
		$cat = D('product_cat');
		
		$prefix = C('DB_PREFIX');

		$catdata = $cat-> query('select c.*,pc.name pcname from ' . $prefix . 'product_cat c left join ' . $prefix . 'product_cat pc on c.cid = pc.id where c.id = ' . $id);

		if(!empty($catdata)){
			
			$this -> assign('catdata', $catdata[0]);
		
			$this -> display();
			
		}else $this -> error('类型不存在');
	
	}
	
	public function editcatdata(){
	
		$data = I('post.');
	
		if(empty($data['name'])){
	
			$this -> error('分类名称不能为空');
	
		}
	
		if(empty($data['keywords'])) $this -> error('关键字不能为空');
	
		if(empty($data['description'])) $this -> error('简单描述不能为空');
		
		if(!preg_match('/^\d+$/', $data['sort'])) $this -> error('排序须是正整数');
		
		foreach($data as $key => $val){
		
			$data[$key] = stopDefaultTag($val);
		
		}
	
		$cat = D('product_cat');
	
		$result = $cat -> save($data);
	
		if(empty($result)){
	
			$this -> error('编辑失败');
	
		}else $this -> success('编辑成功', U('Product/cat'));
	
	}
	
	public function delcat(){
	
		$flag = true;
		
		$id = intval($_GET['id']);
		
		$cat = D('product_cat');
		
		$product = D('product');
		
		$subCids = $cat -> where(array('cid' => $id)) -> count();
		
		$pros = $product -> where(array('cid' => $id)) -> find();

		if(empty($id) || !empty($pros) || $subCids) $flag = false;
		
		else{
			
			$result = $cat -> delete($id);
			
			if(empty($result)) $flag = false;
			
		}
	
		if($flag) $this -> success('删除成功', U('Product/cat'));
	
		else $this -> error('删除失败,该分类下有子类或产品');
	
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