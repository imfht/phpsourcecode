<?php
$data_format_type = isset($_REQUEST['data_format_type']) && !empty($_REQUEST['data_format_type']) ?  $_REQUEST['data_format_type'] : 'json';
switch($do){
	//DEBUG 注销退出
	case "logout":
		ext::synlogout();
		$return = array(
			'errcode'=>'e_0000',
			'errmsg'=>lang('error','e_0000'),
	    	'data'=>''
		);
		echo format_data($return,$data_format_type);
	break;
	
	//DEBUG 登录
	case "login":
		/*
		* TODO 登录队列处理 ZeroMQ Redis ActiveMQ 处理 获取部署第三方软件处理
		*/
		/* RECEIVE VALUE */
		$user_name=isset($_REQUEST['user_name']) ? $_REQUEST['user_name']:'';
		$user_password=isset($_REQUEST['user_password']) ? $_REQUEST['user_password']:'';
		$api_client=isset($_REQUEST['api_client']) ? $_REQUEST['api_client']:'';
		$location_url=isset($_REQUEST['location_url']) ? $_REQUEST['location_url']:'';
		$return = array(
			'errcode'=>'e_1001',
			'errmsg'=>lang('error','e_1001'),
	    	'data'=>''
		);
		if($user_name && $user_password){
			//校验用户或者编号是否存在
			$check_user_info = array();
			$check_user_info = DB::fetch_first("SELECT user_id,user_password FROM ".DB::table('users')." WHERE user_name ='".$user_name."' AND isdelete = 0 LIMIT 1");
			if(empty($check_user_info)){
				$check_user_info = DB::fetch_first("SELECT user_id,user_password FROM ".DB::table('users')." WHERE user_id ='".$user_name."' AND isdelete = 0 LIMIT 1");
			}
			//校验密码
			if($check_user_info['user_id']){
				$user_encode_password = encode_password($user_password);
				if($user_encode_password == $check_user_info['user_password']){
					if($api_client){
						allow_crossdomain();
						$userinfo=DB::fetch_first("SELECT * FROM ".DB::table('users')." WHERE user_id ='".$check_user_info['user_id']."' LIMIT 1");
						//并返回用户信息
						$return = array(
							'errcode'=>'e_1000',
							'errmsg'=>lang('error','e_1000'),
					    	'data'=>$userinfo
						);
					}elseif($location_url){
						$user['user_id']=$check_user_info['user_id'];
						ext::synlogin($user,$user);
						header('location:'.$location_url.'');
						die;
					}else{
						$user['user_id']=$check_user_info['user_id'];
						ext::synlogin($user,$user);
						$return = array(
							'errcode'=>'e_1000',
							'errmsg'=>lang('error','e_1000'),
					    	'data'=>''
						);
					}
				}
			}
		}
		echo format_data($return,$data_format_type);
	break;
}
?>