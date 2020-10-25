<?php 
namespace Admin\Controller;
use Think\Controller;

	class LoginController extends Controller
	{
		
		public function index(){
			$this->display();
		}
		public function login(){
			$user = D('admin')->field("id,name,allow")->where(array("name"=>$_POST["username"],"password"=>md5($_POST["password"])))->find();
			if($user){
				session_start();
				$_SESSION["user"]=$user;
				$_SESSION["isLogin"]=1;
				$this->success("登陆成功",__MODULE__."/Index/index");
			}else {
				$this->error("账号或密码错误",__MODULE__."/Index/index");
			} 

		}	
		public function logout(){
			$username = $_SESSION["user"]["user"];

			session_destroy();

			$_SESSION=array();

			if(isset($_COOKIE[session_name()])){
				setcookie(session_name,time()-3600,'/');
			}

			$this->success($username."再见");
		}
	}
 ?>