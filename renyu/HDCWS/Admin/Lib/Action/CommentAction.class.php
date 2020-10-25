<?php

//留言类
class CommentAction extends GlobalAction {
    
	public function getList(){

		$start = intval($_GET['start']);
		
		$limit = intval($_GET['limit']);
		
		$key = $_GET['key'];
		
		$condition = ' where (p.title like "%' . $key . '%" or p.content like "%' . $key . '%") ';
		
		if(empty($start)) $start = 0;
		
		if(empty($limit)) $limit = 20;
		
		$com = D('comment');
		
		$count = $com -> query('select count(*) counts from ' . C('DB_PREFIX') . 'comment p ' . $condition);

		$count = empty($count) ? 0 : $count[0]['counts'];

		$sql = 'select * from ' . C('DB_PREFIX') . 'comment p ' . $condition . ' order by time desc limit '. $start . ',' . $limit;
		
		$list = $com -> query($sql);
		
		echo json_encode(array('list' => $list, 'total' => $count, 'success' => true));
	
	}
	
	//删除
	public function del(){
	
		$id = $_POST['id'];
	
		$result = 1;
	
		$prefix = C('DB_PREFIX');
	
		if(preg_match('/\d(\,\d)*/', $id)){
	
			$com = D('comment');

			$result = $com -> delete($id);
				
			$result = $result > 0 ? 1 : 0;
				
		}else $result = 0;
	
		echo $result;

	}

}