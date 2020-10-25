<?php
class mobileController extends baseController{
	protected $layout = 'layout';
	
	public function usercenter(){
		$this->display();
	}
	
	public function card(){
		$ppid = $_GET['ppid'];
		$cid = $_GET['cid'];
		$this->cardinfo = model('usercenter')->cardinfo(array('ppid'=>$ppid,'id'=>$cid));
		$this->display();
	}
	
	public function qiandao(){
		$this->display();
	}
	
	public function duihuan(){
		$this->display();
	}
		
	public function message(){
		$this->display();
	}
	
	public function userinfo(){
		$this->display();
	}
	
}