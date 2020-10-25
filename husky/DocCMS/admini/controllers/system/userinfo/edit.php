<?php
/*
 * 编辑管理员用户
 */
	global $request;
	global $db, $user;
	if($request['cid']>0)
	{
		$sql="SELECT * FROM ".TB_PREFIX."user WHERE id=".$request['cid'];
		$user = $db->get_row($sql);
		//if($user->role>8)exit('forbiden');
	}else{
		exit('Forbidden');
	}
	if($_POST)
	{
		if($request['cid']>0)
		{
			$request['role']=intval($request['role']);
			if($_SESSION[TB_PREFIX.'admin_userID']==$request['cid']){//修改自身
				if($_SESSION[TB_PREFIX.'admin_roleId']<$request['role'])
				{
					die('error:您的权限无法操作此角色');
				}
			}else{
				if(($_SESSION[TB_PREFIX.'admin_roleId']-1)<$request['role']){
					die('error:您的权限无法操作此角色');
				}
			}
			if(!isset($request['auditing']) || empty($request['auditing']))
			{
				$request['auditing'] = '0';
			}else{
				$request['auditing']='1';
			}
			require(ABSPATH.'/inc/class.validate.php');
			if(!validate::password(5,16, $request['username'])){
				die('用户名长度5至16位！');
		    }
			$request['nickname']=get_str($request['nickname']);//别名
			if(isset($request['username']))$request['username']=null;//用户名
			$old_user=$user;
			$user = new user();
			$user->get_request($request);
			if(empty($request['pwd']))
			{
				$user->pwd = null;
				$pwd = '';
			}else{
				require_once(ABSPATH.'/inc/class.docencryption.php');
				$docEncryption = new docEncryption($request['pwd']);
				$user->pwd =$request['pwd']= $docEncryption->to_string();
				$docEncryption=null;
				
				$pwd ="`pwd`='".$user->pwd."',";
			}
			if(!empty($_FILES['uploadfile']) && $_FILES['uploadfile']['size']>0 && $_FILES['uploadfile']['size']<200000)
			{
				del_old_file($old_user->originalPic);
				del_old_file($old_user->smallPic);
				del_old_file($old_user->cropPic);
				require_once(ABSPATH."/inc/class.upload.php");
				$upload = new Upload();
				$upload->AllowExt='jpg|jpeg|gif|bmp|png';
				$fileName = $upload->SaveFile('uploadfile');
				
				if(empty($fileName))echo $upload->showError();				
				require_once(ABSPATH."/inc/class.paint.php");
				$paint = new Paint(UPLOADPATH.$fileName);
				$user->originalPic =UPLOADPATH.$fileName;
				$user->smallPic=$paint->Resize(moduleUserWidth,moduleUserHight,'s_');				
			}
			if(!empty($request['url']))
			{
				del_old_file($old_user->cropPic);
				require_once(ABSPATH."/inc/class.paint.php");
				$paint = new Paint($request['url']);
				$user->cropPic =$paint->crop();
			}
			$user->auditing=$request['auditing'];
			$user->id=$request['cid'];
			
			$user->save();

			$user=null;
			echo '<script>alert("修改成功。");</script>';
			redirect("./index.php?m=system&s=userinfo&a=edit&cid=".$request['cid']);
		
		}else{
			exit('Forbidden');
		}
	}
?>