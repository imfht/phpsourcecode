<?php
    /**
	 * @author swu_mao
	 * @time 2014.12.29
	 * 这个类是系统自动生成的类
	*/
	class IndexAction extends Action {
		/**
		 * @param
		 * @return
		 * 首页
		*/
	    public function index(){
	    	if(!isset($_SESSION['username'])){
	    		$this->redirect('Public/login', 2, 2, '您还没有登录，正在跳转到登陆页面！');
	    	}
	    	$User = new UserModel();
	    	$this->assign('name', $User->getName($_SESSION['username']));
			$this->display();
		}
	}
?>