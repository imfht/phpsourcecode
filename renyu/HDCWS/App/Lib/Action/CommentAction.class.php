<?php

class CommentAction extends GlobalAction {

	public function index(){
		
		$this -> assign('title', '访客留言');

		$this -> display();

	}
	
	public function add(){

		$title = despiteStr($_POST['title']);
		
		$content = despiteStr($_POST['content']);
		
		$verify = $_POST['verify'];
		
		if($_SESSION['verify'] != md5($verify)){
		
			$this -> error('验证码错误');
		
		}else{
		
			$comment = D('comment');
			
			$result = $comment -> add(array('title' => $title, 'content' => $content, 'time' => date('Y-m-d H:i:s')));
			
			if($result) $this -> success('留言成功');
			
			else $this -> error('留言失败');
		
		}

	}	

}