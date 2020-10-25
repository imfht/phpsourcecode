<?php
global $db;
	global $request ;
	global $users;
	$sql="SELECT * FROM `".TB_PREFIX."user` WHERE  role>6 AND role<=".$_SESSION[TB_PREFIX.'admin_roleId'];
	$sb = new sqlbuilder('mdt',$sql,'id',$db,20);
	$users = new DataTable($sb,'管理者列表');
	$users->add_col('编号','id','db',40,'$rs[id]');
	$users->add_col('昵称','nickname','db',60,'$rs[nickname]');
	$users->add_col('用户名','username','db',200,'$rs[username]');
	$users->add_col('等级','role','db',0,'getLevel($rs[role])');
	$users->add_col('姓名','name','db',0,'$rs[name]');
	$users->add_col('邮箱','email','db',0,'$rs[email]');
	$users->add_col('手机','mtel','db',0,'$rs[mtel]');
	$users->add_col('地址','address','db',0,'$rs[address]');
	$users->add_col('注册日期','dtTime','db',180,'$rs[dtTime]');
	$users->add_col('审核','audit','text',140,'getAudit($rs[auditing],$rs[id])');
	$users->add_col('操作','edit','text',140,'getNav($rs[id])');
	$users->add_col('权限','Permissions','text',200,'getNavPermissions($rs[id],$rs[role])');
?>