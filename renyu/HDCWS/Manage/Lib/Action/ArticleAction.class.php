<?php

//文章类
class ArticleAction extends GlobalAction {

	private $tid = 1;//文章类型id,1为普通文章类,2为公司团队类,3为关于公司
	
	private $mid = 1;//文章模型id,1为普通,2为单页,3为特殊图片	

	public function index(){

		import('ORG.Util.Page');
		
		$cat = D('article_cat');

		$catList = $cat -> order('sort desc') -> select();
		
		$this -> assign('catList', $catList);		

		$art = D('article');
		
		$key = $_GET['key'];
		
		$cid = intval($_GET['cid']);
		
		$cidStr = empty($cid) ? '' : ' and p.cid = ' . $cid;
		
		$condition = ' where (p.title like "%' . $key . '%" or p.keywords like "%' . $key . '%" or p.description like "%' . $key . '%")' . $cidStr . ' ';
		
		$count = $art -> query('select count(*) counts from ' . C('DB_PREFIX') . 'article p ' . $condition);
		
		$count = empty($count) ? 0 : $count[0]['counts'];
		
		$Page = new Page($count, 20);

		$show = $Page -> show();

		$sql = 'select p.*,c.name cname from ' . C('DB_PREFIX') . 'article p left join ' . C('DB_PREFIX') . 'article_cat c on p.cid = c.id ' . $condition . ' order by time desc limit '. $Page -> firstRow . ',' . $Page -> listRows;

		$list = $art -> query($sql);
		
		$this -> assign('list', $list);

		$this -> assign('pageLink', $show);

		$this -> display();
	
	}
	
	public function add(){
		
		$tid = intval($_GET['tid']);
		
		$cat = D('article_cat');

		$catList = $cat -> order('sort desc') -> select();
		
		$this -> assign('catList', $catList);

		if($tid == 3){
		
			$this -> addabout();
		
		}else if($tid == 2){
		
			$this -> addteam();
		
		}else{

			$this -> display();
		
		}
	
	}
	
	protected function addteam(){
	
		$this -> display('addteam');
	
	}
	
	protected function addabout(){
	
		$this -> display('addabout');
	
	}	
	
	public function addarticle(){
		
		$data = I('post.');

		if(empty($data['title'])){
			
			if($data['tid'] == 2) $this -> error('标题不能为空');
			
			else $this -> error('标题不能为空');
			
		}
		
		if(empty($data['keywords'])) $this -> error('关键字不能为空');
		
		if(empty($data['description'])) $this -> error('简单描述不能为空');
		
		if($data['cid'] == '') $this -> error('类型不能为空');
		
		if(empty($data['content'])) $this -> error('内容不能为空');
		
			if(empty($data['thumburl'])){
			
			if($data['tid'] == 2) $this -> error('请上传大头贴');
			
			else $this -> error('请上传缩略图');
			
		}
		
		$data['content'] = I('content', '', '');
		
		foreach($data as $key => $val){
		
			$data[$key] = stopDefaultTag($val);
		
		}
		
		$article = D('article');
		
		$data['time'] = date('Y-m-d H:i:s');
		
		$result = $article -> add($data);
		
		if(empty($result)){
		
			$this -> error('添加失败');
			
		}else $this -> success('添加成功', U('Article/index'));
		
	}
	
	public function edit(){
		
		$id = intval($_GET['id']);
		
		$tid = intval($_GET['tid']);
		
		$cat = D('article_cat');
		
		$catList = $cat -> order('sort desc') -> select();
		
		$this -> assign('catList', $catList);
		
		$article = D('article') -> where('id='. $id) -> find();
		
		$this -> assign('article', $article);
		
		if($tid == 3){
		
			$this -> editabout();
		
		}else if($tid == 2){
		
			$this -> editteam();
		
		}else{
		
			$this -> display();
		
		}
	
	}
	
	protected function editteam(){
	
		$this -> display('editteam');
	
	}
	
	protected function editabout(){
	
		$this -> display('editabout');
	
	}
	
	public function editarticle(){
	
		$data = I('post.');
		
		if(empty($data['id'])){
		
			$this -> error('文章不存在');
		
		}
	
		if(empty($data['title'])){
				
			if($data['tid'] == 2) $this -> error('标题不能为空');
				
			else $this -> error('标题不能为空');
				
		}
	
		if(empty($data['keywords'])) $this -> error('关键字不能为空');
	
		if(empty($data['description'])) $this -> error('简单描述不能为空');
	
		if($data['cid'] == '') $this -> error('类型不能为空');
	
		if(empty($data['content'])) $this -> error('内容不能为空');
	
		if(empty($data['thumburl'])){
				
			if($data['tid'] == 2) $this -> error('请上传大头贴');
				
			else $this -> error('请上传缩略图');
				
		}
		
		$data['content'] = I('content', '', '');
		
		foreach($data as $key => $val){
		
			$data[$key] = stopDefaultTag($val);
		
		}

		$article = D('article');
	
		$result = $article -> save($data);

		if(empty($result)){
	
			$this -> error('修改失败');
				
		}else $this -> success('修改成功', U('Article/index'));
	
	}
	
	public function del(){
		
		$id = $_GET['id'];
		
		if(is_array($id)) $id = implode(',', $id);

		$result = 1;
		
		$prefix = C('DB_PREFIX');
		
		if(preg_match('/\d(\,\d)*/', $id)){

			$pro = D('article');
			
			$result = $pro -> delete($id);
			
			$result = $result > 0 ? 1 : 0;
			
		}else $result = 0;

		if($result) $this -> success('删除成功', U('Article/index'));
		
		else $this -> error('删除失败');

	}	

	public function cat(){
		
		import('ORG.Util.Page');
		
		$cat = D('article_cat');
		
		$prefix = C('DB_PREFIX');
		
		$list = $cat-> query('select c.*,pc.name pcname from ' . $prefix . 'article_cat c left join ' . $prefix . 'article_cat pc on c.cid = pc.id order by sort desc');
		
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
			
			$cat = D('article_cat');
				
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
		
		$cat = D('article_cat');
		
		$result = $cat -> add($data);
		
		if(empty($result)){
		
			$this -> error('添加失败');
				
		}else $this -> success('添加成功', U('Article/cat'));
	
	}
	
	public function editcat(){
		
		$id = intval($_GET['id']);
		
		$cat = D('article_cat');
		
		$prefix = C('DB_PREFIX');

		$catdata = $cat-> query('select c.*,pc.name pcname from ' . $prefix . 'article_cat c left join ' . $prefix . 'article_cat pc on c.cid = pc.id where c.id = ' . $id);

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
	
		$cat = D('article_cat');
	
		$result = $cat -> save($data);
	
		if(empty($result)){
	
			$this -> error('编辑失败');
	
		}else $this -> success('编辑成功', U('Article/cat'));
	
	}
	
	public function delcat(){
	
		$flag = true;
		
		$id = intval($_GET['id']);
		
		$cat = D('article_cat');
		
		$article = D('article');
		
		$subCids = $cat -> where(array('cid' => $id)) -> count();
		
		$arts = $article -> where(array('cid' => $id)) -> find();

		if(empty($id) || !empty($arts) || $subCids) $flag = false;
		
		else{
			
			$result = $cat -> delete($id);
			
			if(empty($result)) $flag = false;
			
		}
	
		if($flag) $this -> success('删除成功', U('Article/cat'));
	
		else $this -> error('删除失败,该分类下有子类或文章');
	
	}
	
	//上传缩略图
	public function uploadThumbImg(){

		$uploadDir = C('UPLOAD_Article_Dir');

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
	
	public function uploadJson(){
		
		$url = C('UPLOAD_Article_Dir') . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
		
		$save_url = __ROOT__ . '/' . $url;
		
		$save_path = C('HDCWS_DIR') . $url;
	
		upload_json($save_path, $save_url);
	
	}
	
	public function fileManagerJson(){
		
		$url = C('UPLOAD_Article_Dir') . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
		
		$save_url = __ROOT__ . '/' . $url;
		
		$save_path = C('HDCWS_DIR') . $url;		
	
		file_manager_json($save_path, $save_url);
	
	}
	
}