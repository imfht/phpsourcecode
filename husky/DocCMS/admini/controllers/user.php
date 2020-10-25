<?php
function index()
{
	global $users,$db,$request;
	$sql="SELECT * FROM `".TB_PREFIX."user` WHERE role<7 ";
	$sb = new sqlbuilder('mdt',$sql,'id',$db,20);
	$users = new DataTable($sb,'会员列表');
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
}

function create()
{
	global $db,$request;
	if($_POST)
	{
		$request['role']=intval($request['role']);
		if($request['role']>6)
		{
			exit('无效的注册会员的级别');
		}
		if($request['pwd']!=$request['repwd'])
		{
			exit(' 两次密码不一致！');
		}
		$user = new user();
		$user->get_request($request);
		$user->dtTime = date("Y-m-d H:i:s");
		
		require_once(ABSPATH.'/inc/class.docencryption.php');
		$docEncryption = new docEncryption($request['pwd']);
		$user->pwd = $docEncryption->to_string();

		$user->addnew();

		if($user->save())
			redirect('?p='.$request['p']);
		else
			echo '添加失败！';
	}
}
function edit()
{
	global $db,$request;
	if($request['cid']>0)
	{
		global $user;
		$sql='SELECT * FROM `'.TB_PREFIX.'user` WHERE `id` = '.$request['cid'];
		$user = $db->get_row($sql);
		if($user->role>6)exit('Forbidden:此账号为管理者所有，在此禁止操作');
	}else{
		exit('Forbidden');
	}
	if($_POST)
	{
		$request['role']=intval($request['role']);
		if($request['role']>6)
		{
			exit('无效的注册会员的级别');
		}
		if(!isset($request['auditing']) || empty($request['auditing']))
		{
			$request['auditing'] = '0';
		}else{
			$request['auditing']='1';
		}
		$user = new user();
		$user->get_request($request);
		
		if(empty($request['pwd'])){
			$user->pwd = null;
		}else{
			require_once(ABSPATH.'/inc/class.docencryption.php');
			$docEncryption = new docEncryption($request['pwd']);
			$user->pwd = $docEncryption->to_string();
		}
		$user->id=$request['cid'];
		
		if($user->save()!==false)
			redirect('?p='.$request['p']);
		else
			echo '修改失败！';
	}
}
function destroy()
{
	global $db,$request;
	if(!empty($request['cid']))
	{
		 $sql='DELETE FROM `'.TB_PREFIX.'user` WHERE role<7 AND `id`='.$request['cid'].' LIMIT 1';
		if($db->query($sql))
		{
			redirect('?p='.$request['p']);
		}
		else {
			echo '删除失败！';
		}
	}
}
function judgeUserName(){
	
	global $db, $request;
	if(strsafe($request['username'])){
		die('illegal:用户名含有非法信息！');
	}else{
		$username=$request['username'];
		$sql="SELECT count(*)  FROM ".TB_PREFIX."user WHERE username='{$username}'";
		if($db->get_var($sql)){
			die('error:用户名已经存在，请换用一个！');
		}else{
			die('ok:恭喜，该用户名可用！');
		}
	}
}
function strsafe($str,$flag=false){	//返回true 为非法 返回false为合法数据
	// /^[^####]*$/  ##为替换的特殊字符
	$parnt='/^[^\[\]\{\}\+\*\|\^\$\?"\'<>%]*$/';
	if(!$flag){
		if(empty($str)) return true;
	} 
	if(!preg_match ($parnt,$str)) return true;
	$parnt2='/(select)|(update)|(insert)|(create)|(delete)/';
	$str=strtolower($str);
	return preg_match($parnt2,$str);
}

function audit_cancel()
{
	audit('0');
}
function audit_pass()
{
	audit('1');
}
function audit($autit='0'){
	global $db;
	global $request;
	$sql="UPDATE ".TB_PREFIX."user SET `auditing` = '".$autit."' WHERE role<7 AND id=".$request['cid']." limit 1";
	if($db->query($sql))
	{
		redirect('?p='.$request['p']);
	}
	else {
		echo '审核失败！';
	}
}
function getAudit($audit,$id){
	global $request;
	if($audit)
	return "<a href=\"./index.php?p=".$request['p']."&a=audit_cancel&cid=".$id."\">取消</a>";
	else 
	return "<a href=\"./index.php?p=".$request['p']."&a=audit_pass&cid=".$id."\">审核</a>";
}
function getNav($id){
	global $request;
	return "<a href=\"./index.php?p=".$request['p']."&a=edit&cid=".$id."\">[修改]</a>
		|<a href=\"./index.php?p=".$request['p']."&a=destroy&cid=".$id."\" onclick=\"return confirm(\'你确定要删除么？\');\">[删除]</a>";
}
function getLevel($role){
	$level = array( '1'=>'普通会员','2'=>'vip1','3'=>'vip2','4'=>'vip','5'=>'vip4');
	return $level[$role];
}
?>