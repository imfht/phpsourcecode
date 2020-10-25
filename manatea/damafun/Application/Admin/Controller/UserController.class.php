<?php
namespace Admin\Controller;
use Think\Controller;
/**
* 
*/
class UserController extends CommonController
{
	//用户管理界面
	public function index(){
		$data = D("user")->select();
		$this->assign("user",$data);
		$this->display();
	}
	//用户信息管理
	public function mod(){
		$user = D("user")->where(array('id'=>$_GET['id']))->find();
		$this->assign("user",$user);
		$this->display();
	}
	//用户权限切换
	public function allow(){
		$_POST['allow']=$_GET['allow']==1?0:1;
		if(D("user")->where(array('id'=>$_GET['id']))->save($_POST))
			$this->success("修改成功",__CONTROLLER__."/index");
		else $this->error("修改失败",__CONTROLLER__."/index");
	}
}
?>