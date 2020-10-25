<?php
/*
 * 登录操作类
 *
 * @copyright   Copyright (C) 2017-2018 07FLY Network Technology Co,LTD (www.07FLY.com) All rights reserved.
 * @license     For licensing, see LICENSE.html or http://www.07fly.top/crm/license
 * @author      kfrs <goodkfrs@QQ.com>
 * @package     admin.Book
 * @version     1.0
 * @link       http://www.07fly.top
 */	 
class Login extends Action{	
	private $cacheDir='';//缓存目录
	public function __construct() {	
		//_instance('Action/Auth');
	}		
	
	public function login(){
		$config = $this->get_sys_config();
		
/*		if(is_mobile()){
			$login_mbfile = "wxapp/login.html";
		}else{
			$login_mbfile = "login.html";
		}*/
		$login_mbfile = "login.html";
		if(empty($_POST)){
			$smarty  = $this->setSmarty();
			$smarty->assign(array('sys'=>$config));
			$smarty->display($login_mbfile);	
		}else{
			if($this->login_auth()){
//				if(is_mobile()){
//					$this->location("",'wxapp/WxIndex/index/',0);		
//				}else{
//					$this->location("",'Index',0);			
//				}
				$this->location("",'/Index',0);			
			}else{
				$list = array("sys"=>$config,
								"txtinfo"=>"输入的信息有误~~",
								"username"=>$_POST["username"],
								"password"=>$_POST["password"]
							);
				$smarty = $this->setSmarty();
				$smarty->assign($list);
				$smarty->display($login_mbfile);					
			}
		}
	}

	public function login_auth(){
		$username 	 = $_POST["username"];
		$password 	 = $_POST["password"];
		$sql 		 = "select * from fly_sys_user where account='$username' and password='$password'";	
		$one 		 = $this->C($this->cacheDir)->findOne($sql);
		if(!empty($one)){
	
			//定义SESSION变量值
			$_SESSION["CRM"]["USER"]["account"]		= $username;
			$_SESSION["CRM"]["USER"]["userID"]		= $one["id"];
			

			@define('SYS_USER_ACCOUNT',$_SESSION["CRM"]["USER"]["account"]);//定义
			@define('SYS_USER_ID', $_SESSION["CRM"]["USER"]["userID"]);//定义
			
	
			$role=_instance('Action/User')->user_get_power($one["id"]);
			//print_r($role);
			//权限返回值为一维数组，为系统用户私有数据
			$_SESSION["CRM"]["USER"]["menu"]		= explode(",",implode(",",($role["SYS_MENU"]) ) );
			$_SESSION["CRM"]["USER"]["menustr"]		= implode(",",($role["SYS_MENU"]) );
			$_SESSION["CRM"]["USER"]["area"]		= explode(",",implode(",",($role["SYS_AREA"]) ) );
			$_SESSION["CRM"]["USER"]["areastr"]		= implode(",",($role["SYS_AREA"]) );
			
			$_SESSION["CRM"]["USER"]["method"]		= explode(",",implode(",",($role["SYS_METHOD"]) ));
			$_SESSION["CRM"]["USER"]["methodstr"]	= implode(",",($role["SYS_METHOD"]) );
			
			//这是得到系统权限需要检查的总表
			$_SESSION["CRM"]["NEED"]["menu"] 		= $this->L("Menu")->menu_auth_arr();
			$_SESSION["CRM"]["NEED"]["method"] 		= $this->L("Method")->method_auth_arr();
			
			$_SESSION["CRM"]["USER"]["viewID"]		= implode(",",$this->L("User")->user_get_sub_user($one["id"]));
			@define('SYS_USER_VIEW',$_SESSION["CRM"]["USER"]["viewID"]);//定义查看的权限		
			//print_r(_instance('Action/Menu')->menu_auth_arr());
			//print_r($_SESSION["CRM"]["NEED"]["menu"]);
			
//			print_r($_SESSION["CRM"]["USER"]["menu"]);
//			exit;
			
			return true;
		}else{
			return false;
		}
	}
	
	
	public function logout(){	
		unset($_SESSION["CRM"]);
		$this->location("",'Login/login',0);		
	}
	
	public function sys_serial(){
		return array(
			"MAXUSER"=>2000,
			"MAXNAS"=>10,
		);	
	}
	//得到系统配置参数
	public function get_sys_config(){
		$sql 	= "select * from fly_sys_config;";
		$list	= $this->C($this->cacheDir)->findAll($sql);
		
		if(is_array($list)){
			foreach($list as $key=>$row){
				$assArr[$row["name"]] = $row["value"];
			}
		}
		return $assArr;		
	}	
	//判断用户超过限制没有
	//return true / false
	public function check_user_max(){
		$maxuser =$this->sys_serial();
		$countSql='select * from userinfo';
		$totalNum=$this->C($this->cacheDir)->countRecords($countSql);	//计算记录数	
		if($totalNum>=$maxuser['MAXUSER']){
			return false;
		}else{
			return true;
		}
	}
	public function version(){
		 $this->show('version'); 	 		 
	}

	public function cookie_session(){
		setcookie('user','test_cookie');
		$_SESSION ["UID"] = 'test_session';
		$this->location('Test/_cookie_session');
	}
	public function _cookie_session(){
			$str='这是输出值：'.$_COOKIE['user'].'<br>'.$_SESSION['UID'];
			$this->assign('str',$str);
			$this->show('test');
	}


}//end Class

?> 