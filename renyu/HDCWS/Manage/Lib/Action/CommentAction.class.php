<?php

class CommentAction extends GlobalAction {

	public function index(){
		
		import('ORG.Util.Page');
		
		$comment = D('comment');
		
		$key = $_GET['key'];
		
		$condition = ' where (p.title like "%' . $key . '%" or p.content like "%' . $key . '%") ';		
		
		$count = $comment -> query('select count(*) counts from ' . C('DB_PREFIX') . 'comment p ' . $condition);

		$count = empty($count) ? 0 : $count[0]['counts'];
		
		$Page = new Page($count, 20);
		
		$show = $Page -> show();

		$sql = 'select * from ' . C('DB_PREFIX') . 'comment p ' . $condition . ' order by time desc limit '. $Page -> firstRow . ',' . $Page -> listRows;
		
		$list = $comment -> query($sql);		
		
		$this -> assign('list', $list);
		
		$this -> assign('pageLink', $show);
		
		$this -> display();

	}
	
	public function viewcomment(){
	
		$id = intval($_GET['id']);
	
		$data = D('comment') -> where('id='. $id) -> find();
	
		$this -> assign('data', $data);
	
		$this -> display();
	
	}
	
	public function del(){
	
		$id = $_GET['id'];
	
		if(is_array($id)) $id = implode(',', $id);
	
		$result = 1;
	
		if(preg_match('/\d(\,\d)*/', $id)){
	
			$comment = D('comment');
	
			$result = $comment -> delete($id);
	
			$result = $result > 0 ? 1 : 0;
	
		}else $result = 0;
	
		if($result) $this -> success('删除成功', U('Comment/index'));
	
		else $this -> error('删除失败');
	
	}
	
}