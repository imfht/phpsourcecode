<?php 
namespace Admin\Controller;
use Think\Controller;
class AdminController extends CommonController{
	//管理员界面
	public function index()
	{
		$data = D("admin")->select();
		$this->assign("data",$data);
		$this->display();
	}

	//管理员添加界面
	public function add(){
		$this->display();
	}

	public function insert(){
		$admin = D('admin');
		if($admin->where(array("name"=>$_POST['name']))->find())
			$this->error("用户名已存在",__CONTROLLER__."/add");
		if($admin->create()){
			$admin->add();
			$this->success("添加成功",__CONTROLLER__."/index");
		}
		$this->error("添加失败：{$admin->getError()}",__CONTROLLER__."/add"); 
	}
	//管理员删除
	public function delete(){
		if(D('admin')->where("allow != 1 and id = %d",$_GET['id'])->delete())
			$this->success("删除成功",__CONTROLLER__."/index");
		$this->error("删除失败或者权限不够",__CONTROLLER__."/index");
	}
}
 ?>