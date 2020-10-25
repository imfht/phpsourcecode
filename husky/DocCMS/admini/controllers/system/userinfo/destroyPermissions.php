<?php
/**
 * 删除管理者的信息
 * @global $request
	 * 如果检测管理菜单的文件是否存在 存在则删除; 否则提示文件已删除
	 */
global $request;
	if(intval($request['id'])<1)
	{
		unset($request);
		die('error:非法操作，用户不存在！');
	}
	else
	{
		$path=ABSPATH.'/admini/controllers/system/userinfo/config/';
		$filename=$path.'dt-RightsManagement-config-'.$request['id'].'.php';
		if(is_file($filename))
		{
			@unlink($filename);
		}
		$filename=$path.'nav_'.$request['id'].'.php';
		if(is_file($filename))
		{
			@unlink($filename);
		}
		$filename=$path.'menu_content_'.$request['id'].'.js';
		if(is_file($filename))
		{
			@unlink($filename);
		}
		unset($request);
		redirect('?m=system&s=userinfo');
		exit;
	}
?>