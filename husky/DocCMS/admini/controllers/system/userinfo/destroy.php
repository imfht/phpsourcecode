<?php
/**
 * 删除管理者的信息
 * @global $request
 */
global $db;
	global $request;
	if(!empty($request['cid']))
	{
		if($request['cid']>1){
			$path=ABSPATH.'/admini/controllers/system/userinfo/config/';
			$filename=$path.'dt-RightsManagement-config-'.$request['cid'].'.php';
			if(is_file($filename))
			{
				@unlink($filename);
			}
			$filename=$path.'nav_'.$request['cid'].'.php';
			if(is_file($filename))
			{
				@unlink($filename);
			}
			$filename=$path.'menu_content_'.$request['cid'].'.js';
			if(is_file($filename))
			{
				@unlink($filename);
			}
			$sql="DELETE FROM ".TB_PREFIX."user WHERE id=".$request['cid']." limit 1";
			$db->query($sql);
			redirect('?m=system&s=userinfo');
		}elseif($request['cid']==1){
			exit('默认超级管理员不允许删除');
		}else{
			exit('Forbidden');
		}
	}
?>