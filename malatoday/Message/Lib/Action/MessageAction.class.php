<?php
    /**
	 * @author swu_mao
	 * @time 2014.12.29
	 * 这个类是留言板系统的核心类，主要处理留言的显示与发布等功能
	*/
	class MessageAction extends Action{
		/**
		 * @param
		 * @return
		 * 发表留言
		*/
		public function add(){
			$User = new UserModel();
	    	$this->assign('name', $User->getName($_SESSION['username']));
			$this->display();
		}

		/**
		 * @param 
		 * @return
		 * 发表留言处理方法
		*/
		public function addHandle(){
			if(session('code')!=md5($_POST['code'])){
				$this->error('验证码输入错误！');
			}
			$Message = new MessageModel();
			$User = new UserModel();
			if($Message->addMessage($this->_post('title'), $this->_post('content'), $User->getUid($_SESSION['username']))){
				$this->success('发布留言成功！', U('Index/index'));
			}
			$this->error('发布留言失败！');
		}

		/**
		 * @param 
		 * @return
		 * 显示留言方法
		*/
		public function show(){
			$Message = new MessageModel();
			$User = new UserModel();
			$tmp = $Message->getAllMessage();
			$data = array();
			foreach ($tmp as $v) {
				$v['name'] = $User->getNameById($v['uid']);
				$data[] = $v;
			}
			$this->assign('data', $data);
			$this->display();
		}
	}
?>