<?php
class Login extends Action {
	private $cacheDir = ''; //缓存目录
	public function main(){
		$smarty =$this->setSmarty();
		if(isset($_COOKIE['member_acct'])){
			$one	=array('account'=>$_COOKIE['member_acct'],'password'=>$_COOKIE['member_pwd']);
			$smarty->assign(array('one'=>$one));
		}else{
			//$smarty->assign(array('need'=>'need'));
		}
		//print_r($_COOKIE);
		$smarty->display('home/login.html');
	}
	
	public function login_check(){
		$account 	 = $this->_REQUEST("account");
		$password 	 = $this->_REQUEST("password");
		if($this->login_auth()){//验证成功
			header("location:/index.php/home/Index");
			//$this->location("",'home/Index',0);				
		}		
	}

	public function login_auth(){
		$account 	 = $this->_REQUEST("account");
		$password 	 = $this->_REQUEST("password");
		$sql 		 = "select * from fly_member where (account='$account' or mobile='$account') and password='$password'";	
		$one 		 = $this->C($this->cacheDir)->findOne($sql);
		$dt			 = session_id(); 
		
		if(!empty($one)){
			$account =$one['account'];
		 	$_SESSION["member_acct"]	= $one['account'] ;
		 	$_SESSION["member_pwd"]		= $one['password'] ;
			$_SESSION["member_id"]		= $one["member_id"] ;
			$_SESSION["member_name"]	= $one["name"] ;;			
			setcookie("member_acct",$one["account"],time()+360000,"/");
			setcookie("member_pwd",$one["password"],time()+360000,"/");
			setcookie("member_id",$one["member_id"],time()+360000,"/");
			setcookie("member_name",$one["name"],time()+360000,"/");
			setcookie("sesseion_txt",$dt,time()+360000,"/");
			$sesseion_txt	=@$_COOKIE['sesseion_txt'];
			$sql="update fly_member set sesseion_txt='$sesseion_txt' where account='$account'";
			$rtn=$this->C($this->cacheDir)->update($sql);	
			$rtn_msg=array('code'=>'sucess','message'=>'验证成功');
		}else{
			$rtn_msg=array('code'=>'fail','message'=>'登录失败,帐号或密码有误');
		}
		echo json_encode($rtn_msg);
	}
	
	
	public function login_out(){	
		unset($_SESSION);
		unset($_COOKIE);
		header("location:/index.php/home/Login/main/");
		//$this->location("",'home/Login/main/',0);	
	}

} //

?>