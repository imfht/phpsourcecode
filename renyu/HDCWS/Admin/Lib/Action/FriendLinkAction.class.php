<?php

//友情链接类
class FriendLinkAction extends GlobalAction {
    
	public function getList(){

		$start = intval($_GET['start']);
		
		$limit = intval($_GET['limit']);
		
		if(empty($start)) $start = 0;
		
		if(empty($limit)) $limit = 20;
		
		$link = D('link');
		
		$count = $link -> count();
		
		$sql = 'select * from ' . C('DB_PREFIX') . 'link order by sort desc limit '. $start . ',' . $limit;
		
		$list = $link -> query($sql);
		
		echo json_encode(array('list' => $list, 'total' => $count, 'success' => true));
	
	}
	
	//添加
	public function add(){
	
		$flag = true;
	
		$data = I('post.');
		
		foreach($data as $key => $val){
		
			$data[$key] = despiteStr($val);
		
		}
	
		$link = D('link');
	
		$result = $link -> add($data);
	
		if(empty($result)) $flag = false;
	
		echo json_encode(array('success' => $flag));
	
	}
	
	//编辑
	public function edit(){
	
		$flag = true;
	
		$data = I('post.');
		
		foreach($data as $key => $val){
		
			$data[$key] = despiteStr($val);
		
		}
	
		$link = D('link');
	
		$result = $link -> save($data);
	
		if(empty($result)) $flag = false;
	
		echo json_encode(array('success' => $flag));
	
	}
	
	//删除
	public function del(){
	
		$id = $_POST['id'];
	
		$result = 1;
	
		$prefix = C('DB_PREFIX');
	
		if(preg_match('/\d(\,\d)*/', $id)){
	
			$link = D('link');

			$result = $link -> delete($id);
				
			$result = $result > 0 ? 1 : 0;
				
		}else $result = 0;
	
		echo $result;

	}

}