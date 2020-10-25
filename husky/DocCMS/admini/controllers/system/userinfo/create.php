<?php
/*
 * 创建管理员用户
	 * 检测用户名 
	 * 如果用户名存在 则禁止创建 否则 创建用
	 */
	global $request;
	if($_POST)
	{
		$request['role']=intval($request['role']);//角色
		if($_SESSION[TB_PREFIX.'admin_roleId']<=$request['role'])
		{
			die('error:您的权限无法操作此角色');
		}
		if($request['pwd']!=$request['repwd'])
		{
			exit(' 两次密码不一致！');
		}
		$user = new user();
		$request['username']=get_str($request['username']);//用户名
		$sql="SELECT count(*) FROM ".TB_PREFIX."user WHERE username='".$request['username']."' ";
		if($user->get_var($sql)>0)
		{
			$user=null;
		 	exit('此用户名已经存在');
		}else{
			$request['nickname']=get_str($request['nickname']);//别名
			$user->addnew($request);
			$user->dtTime = date("Y-m-d H:i:s");
			
			require_once(ABSPATH.'/inc/class.docencryption.php');
			$docEncryption = new docEncryption($request['pwd']);
			$user->pwd = $request['pwd'] = $docEncryption->to_string();//加密密码
			$user->image =empty($request['image'])?process_picture('uploadfile',$user->image):$request['image'];
			$docEncryption=null;
			
			$user->save();
			$user=null;
			redirect('?m=system&s=userinfo');
		}
	}
?>