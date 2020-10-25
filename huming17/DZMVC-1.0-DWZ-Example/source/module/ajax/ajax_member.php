<?php

switch($do){
	case "logout":
		ext::synlogout();
		@header('Location: index.php');
	break;
	case "ajax_login":
		/* RECEIVE VALUE */
		$username=isset($_GET['username']) ? $_GET['username']:'';
		$password=isset($_GET['password']) ? $_GET['password']:'';
		$api_client=isset($_GET['api_client']) ? $_GET['api_client']:'';
		$check_username=DB::result_first("SELECT user_id FROM ".DB::table('users')." where user_name ='".$username."' AND isdelete = 0 LIMIT 1");
		$user_id=$check_user_pass=DB::result_first("SELECT user_id FROM ".DB::table('users')." where user_name ='".$username."' and user_password ='".encode_password($password)."' AND isdelete = 0 LIMIT 1");
		$validateError= lang('core','username_right');
		$validateSuccess= lang('core','username_wrong');
		
		/* RETURN VALUE */
		$arrayToJs = array();
		$arrayToJs[0] = array();
		$arrayToJs[1] = array();
		
		if(!empty($check_username)){
			// validate??
			$arrayToJs[0][0] = 'username';
			$arrayToJs[0][1] = true;
			// RETURN TRUE
			$arrayToJs[0][2] = lang('core','username_effective');
			// RETURN ARRAY WITH success
		}else{
			$arrayToJs[0][0] = 'username';
			$arrayToJs[0][1] = false;
			$arrayToJs[0][2] = lang('core','username_invalid');
		}
		
		if(!empty($check_user_pass)){
			// validate??
			$arrayToJs[1][0] = 'password';
			$arrayToJs[1][1] = true;
			// RETURN TRUE
			// RETURN ARRAY WITH success
		}else{
			$arrayToJs[1][0] = 'password';
			$arrayToJs[1][1] = false;
			$arrayToJs[1][2] = lang('core','password_invalid');
		}
		
		//设置用户登录SESSION
		//if($arrayToJs[1][1] == true && $arrayToJs[0][1] = true ){
		//	@session_start();
		//	$_SESSION['username'] = $username;
		//	$_SESSION['user_role'] = $user_role;
		//	$_SESSION['user_group_id'] = $user_group;
		//	$_SESSION['login_status'] = "1";
		//}else{
		//	@session_start();
		//	$_SESSION['username'] = "guest";
		//	$_SESSION['user_role'] =  "0";
		//	$_SESSION['user_group'] =  "0";
		//	$_SESSION['login_status'] = "0";
		//}
		if($api_client){
			allow_crossdomain();
			$userinfo=DB::fetch_first("SELECT * FROM ".DB::table('users')." where user_id ='".$user_id."' LIMIT 1");
			$arrayToJs['userinfo']=$userinfo;
			echo json_ext($arrayToJs);
			//并返回用户信息
		}else{
			$user['user_id']=$user_id;
			ext::synlogin($user,$user);
			//var_dump($arrayToJs);
			echo json_ext($arrayToJs);
		}
	break;

	case "ajax_reg_check_username":
		/* RECEIVE VALUE */
		$user_name=$_REQUEST['fieldValue'];
		$validateId=$_REQUEST['fieldId'];
		$check_username=DB::result_first("SELECT user_id FROM ".DB::table('users')." where user_name ='".$user_name."' AND isdelete = 0 LIMIT 1");
		$validateError= lang('core','username_exist');
		$validateSuccess= lang('core','username_effective');
		
		/* RETURN VALUE */
		$arrayToJs = array();
		if(empty($check_username)){
			// validate
			$arrayToJs[0] = $validateId;
			$arrayToJs[1] = true;
			// RETURN TRUE
			//$arrayToJs[0][2] = $validateSuccess;
			// RETURN ARRAY WITH success
		}else{
			$arrayToJs[0] = $validateId;
			$arrayToJs[1] = false;
			//$arrayToJs[0][2] = $validateError;
		}
		echo json_ext($arrayToJs);
	break;
	
	case "ajax_userinfo":
		/* RECEIVE VALUE */
		$api_client=isset($_GET['api_client']) ? $_GET['api_client']:'';
		$user_id=isset($_GET['user_id']) ? $_GET['user_id']:'';
		if($api_client){
			allow_crossdomain();
			if($api_client){
				$userinfo=DB::fetch_first("SELECT * FROM ".DB::table('users')." where user_id ='".$user_id."' LIMIT 1");
				$arrayToJs['userinfo']=$userinfo;
				echo json_ext($arrayToJs);
				die;
				//并返回用户信息
			}
		}
	//DEBUG 用户scuore转移
	case "transfer_score":
		/* RECEIVE VALUE */
		$return_array = array('error_code'=>'a001','error_msg'=>'操作失败或您的金币数不够','data'=>array());
		$api_client=isset($_GET['api_client']) ? $_GET['api_client']:'';
		$user_id=isset($_GET['user_id']) ? $_GET['user_id']:'';
		$transfer_user_name=isset($_POST['transfer_user_name']) ? $_POST['transfer_user_name']:'';
		$transfer_user_score=isset($_POST['transfer_user_score']) ? $_POST['transfer_user_score']:'';
		if($api_client){
			allow_crossdomain();
			//DEBUG 判断用户金币是否足够
			$userinfo=DB::fetch_first("SELECT * FROM ".DB::table('users')." where user_id ='".$user_id."' LIMIT 1");
			if(isset($userinfo['user_score']) && $userinfo['user_score'] > $transfer_user_score){
				//DEBUG 执行转换 先扣除用户金币 然后被对方增加金币
				$new_user_score = $userinfo['user_score'] - $transfer_user_score;
				$userinfo=DB::update('users',array('user_score'=>$new_user_score),array('user_id'=>$user_id));
				//DEBUG 取出被添加人user_score
				$target_score=DB::result_first("SELECT user_score FROM ".DB::table('users')." where user_name ='".$transfer_user_name."' LIMIT 1");
				$new_target_score = $target_score + $transfer_user_score;
				$userinfo=DB::update('users',array('user_score'=>$new_target_score),array('user_name'=>$transfer_user_name));
				$return_array = array('error_code'=>'a000','error_msg'=>'操作成功','data'=>array());
			}
			echo json_ext($return_array);
			die;
		}
	break;
}
?>