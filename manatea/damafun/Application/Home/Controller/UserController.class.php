<?php 
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller
{
	//登陆
	public function login()
	{
 		$cat = D("cat")->where("pid = 0")->limit(5)->select();
		$this->assign("cat",$cat);
		$this->display(); 
	}

	//登录
	public function loginCheck(){
			$user = D('user')->field("id,name,allow")->where(array("name"=>$_POST["username"],"password"=>md5($_POST["password"])))->find();
			if($user){
				session_start();
				$_SESSION["user"]=$user;
				$_SESSION["userLogin"]=1;
				$this->success("登陆成功",__MODULE__."/index/index");
			}else {
				$this->error("账号或密码错误",__CONTROLLER__."/login");
			}
	}

	//登出
	public function logout(){
			$username = $_SESSION["user"]["user"];

			session_destroy();

			$_SESSION=array();

			if(isset($_COOKIE[session_name()])){
				setcookie(session_name,time()-3600,'/');
			}

			$this->success($username."再见",__MODULE__."/index/index");
	}
	//注册
	public function register(){
		
		$cat = D("cat")->where("pid = 0")->limit(5)->select();
		$this->assign("cat",$cat);
		$this->display();
		
	}

	//注册操作
	public function registerAction(){
		
		$user = D("user");
		if($user->where(array("name"=>$_POST['name']))->find())
			$this->error("用户名已存在",3,"user/register");
		$_POST['password']=md5($_POST['password']);
		$_POST['repassword']=md5($_POST['repassword']);
		$_POST['allow']=1;
		if($user->create()) {
			$id=$user->add();
			session_start();
			$user = array("id"=>$id,"name"=>$_POST['name'],"allow"=>$_POST['allow']);
			$_SESSION["user"]=$user;
			$_SESSION["userLogin"]=1;
			$this->success("注册成功",__MODULE__."/index/index");
		}
		else $this->error($user->getError(),__MODULE__."/user/register");
	}
}
?>