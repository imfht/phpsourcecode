<?php
/**
 * 管理者中心
 */
function index(){
	checkme(9);
	require_once('userinfo/index.php');
}
function create(){
	checkme(9);
	require_once('userinfo/create.php');
}
function edit(){
	require_once('userinfo/edit.php');	
}
function destroy(){
	checkme(9);
	require_once('userinfo/destroy.php');
}

function assigningPermissions(){
	checkme(9);
	require_once('userinfo/assigningPermissions.php');
}
function destroyPermissions(){
	require_once('userinfo/destroyPermissions.php');
}
function getUserRights(){
	checkme(9);
	require_once('userinfo/getUserRights.php');	
}
function audit_cancel(){
	audit('0');
}
function audit_pass(){
	audit('1');
}
function audit($autit='0'){
	global $db;
	global $request;
	if($request['cid']>0){
		$sql="SELECT * FROM `".TB_PREFIX."user` WHERE id=".$request['cid'];
		$user = $db->get_row($sql);
		if($user->role>8)exit('forbiden');
	}else{
		exit('Forbidden');
	}
	$sql="UPDATE `".TB_PREFIX."user` SET `auditing` = '".$autit."' WHERE id=".$request['cid']." limit 1";
	$db->query($sql);
	redirect('?m=system&s=userinfo');
}
function getAudit($audit,$id){
	global $request;
	if($id>1){
		if($audit)
		return "<a href=\"./index.php?m=system&s=userinfo&a=audit_cancel&cid=".$id."\">取消</a>";
		else 
		return "<a href=\"./index.php?m=system&s=userinfo&a=audit_pass&cid=".$id."\">审核</a>";
	}else{
		return "认证";
	}
}
function isSex($sex){
	return ($sex<2 )?"男":"女";
}
function getLevel($role){
	$level = array( '7'=>'栏目管理员','8'=>'频道管理员','9'=>'超级管理员','10'=>'创始人');
	return $level[$role];
}
function getNav($id){
	$navstr='';
	if($id>1){
		$navstr.= "
		<a href=\"./index.php?m=system&s=userinfo&a=edit&cid=".$id."\">[修改]</a>
		|<a href=\"./index.php?m=system&s=userinfo&a=destroy&cid=".$id."\" onclick=\"return confirm(\'你确定要删除么？\');\">[删除]</a>";
	}else{ 
		$navstr.= "<a href=\"./index.php?m=system&s=userinfo&a=edit&cid=".$id."\">[修改]</a>";
	}
	return $navstr;
}
function getNavPermissions($id,$role){
	$navstr='';
	$navstr.='<a href="./index.php?m=system&s=userinfo&a=getUserRights&id='.$id.'" style="padding-left:5px">[查看]</a>';
	if($role<'9'){
		$navstr.='<a href="./index.php?m=system&s=userinfo&a=assigningPermissions&id='.$id.'" style="padding-left:5px">[分配]</a>';
		$filename=ABSPATH.'/admini/controllers/system/userinfo/config/dt-RightsManagement-config-'.$id.'.php';
		if(is_file($filename)){
			$navstr.='<a href="./index.php?m=system&s=userinfo&a=destroyPermissions&id='.$id.'" onclick="return confirm(\'你确定要删除么？\');" style="padding-left:5px">[删除]</a>';
		}
	}
	return $navstr;
}


function delFolders($path){//递归删除目录
	if (is_dir($path)){//如果文件夹不存在
		if(is_empty_dir($path)){ //如果是空的
			rmdir($path);//直接删除
			delFolders(dirname($path));//取得最后一个文件夹的全路径返回开始的地方
		}else{
			return false;
		}
	}
}
function createFolders($path){//递归创建目录
	if (!is_dir($path)){//如果文件夹不存在
		createFolders(dirname($path));//取得最后一个文件夹的全路径返回开始的地方
		mkdir($path, 0777);
	}
}

function createFile($filename,$content){//创建并写文件
	file_put_contents($filename,$content);//写文件
	chmod($filename,0777);
}

function is_empty_dir($path) { //判断目录是否为空 
	$dirhandle=opendir($path); 
	$i=0; 
	while($tmp=readdir($dirhandle)){ 
		$i++; 
	} 
	closedir($dirhandle); 
	if($i>2){
		return false; 
	}else{
		return true;
	}
} 

/******************************************************/
function judgeUserName(){
	checkme(9);
	global $db, $request;
	if(strsafe($request['username'])){//有时间使用正则进行较为严格的验证
		die('illegal:用户名含有非法信息！');
	}else{
		$username=$request['username'];
		$sql="SELECT count(*)  FROM ".TB_PREFIX."user WHERE username='{$username}'";
		if(strlen($username)<5 || strlen($username)>16){
			die('error:用户名长度 5-16');
		}
		else if($db->get_var($sql)){
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
function get_user_menus($menuIdArray){
	global $db,$substr;
	for($i=0;$i<count($menuIdArray);$i++)
	{
		if($menuIdArray[$i]=='flash')
		$menuIdArray[$i] = 0;
	}
	$sql="SELECT * FROM ".TB_PREFIX."menu WHERE  id in(".@implode(',',$menuIdArray).") order by id asc";//生成js菜单必须以id asc
	$tempObj=$db->get_results($sql);
	if(!empty($tempObj))
	{
	   foreach($tempObj as $menu){	 
	      if(!$menu->deep)
		  {
			  $substr ='';
			  get_user_sub($menu->id,$menuIdArray);
			  $tempstr.= "<li><a href='./index.php?p=$menu->id'>$menu->title</a>".$substr."</li>\r\n";
		  }
	   }
	   if(in_array('0',$menuIdArray))
	   $tempstr.= "<li><a href='./index.php?m=system&s=flashoptions'>系统广告管理 >></a></li>\r\n";	
	}
	return $tempstr;
}
function get_user_sub($id=0,$menuIdArray)
{	
	global $menus,$subs,$substr;    			
	if(!isset($subs[$id])) return; //没有子类,返回空;

	$substr .= '<ul>';
	foreach($subs[$id] as $sid){
		if(in_array($menus[$sid]['id'],$menuIdArray))
		{
			$substr .="<li><a href='./index.php?p=".$menus[$sid]["id"]."'>".$menus[$sid]["title"].'</a>';
			get_user_sub($sid,$menuIdArray);
			$substr .="</li>\r\n";
		}
	}
	$substr .= '</ul>';
}
function get_parent_nodes()
{
	global $db,$request;
	if($request['id']=='flash')
	exit('flash');
	$id=intval($request['id']);
	$sql="SELECT id, parentId, deep FROM `".TB_PREFIX."menu`  ORDER BY deep ASC";
	$menus=$db->get_results($sql);
	foreach(trace_parent_nodes($id,$menus) as $v){
		$arr[]=$v->id;
	}
	exit(implode(',',(array)$arr));
}
function trace_parent_nodes($parentId,$menus){
	if(!$menus)return array();
	foreach($menus as $o)
	{
		if($o->id == $parentId)
		{
			if($o->deep){
				$arr=trace_parent_nodes($o->parentId,$menus);
			}
			$arr[]=$o;
		}
	}
	return $arr;
}
function get_sub_nodes(){
	global $db,$request;
	if($request['id']=='flash')
	exit('flash');
	$id=intval($request['id']);
	$arr=trace_sub_nodes($id);
	exit(implode(',',(array)$arr));
}

function trace_sub_nodes($id){
	global $db;
	$all=get_allmenus();
	$temp=array($id);
	foreach($all as $v){
		foreach($temp as $o){
			foreach($all as $vv){
				if($o==$vv->parentId){
					$arr[]=$vv->id;
				}
			}
		}
		//print_r($temp);
		foreach($temp as $o){
			array_shift($temp);
			foreach($all as $vv){
				if($o==$vv->parentId){
					if($vv->hassub){
						$temp[]=$vv->id;
					}
				}
			}
		}
	}
	return $arr;
}
function get_allmenus(){
	global $db;
	$sql="SELECT id, parentId, deep,(SELECT count(id) FROM `".TB_PREFIX."menu` b WHERE b.parentId=a.id ) hassub FROM `".TB_PREFIX."menu` a ";
	return $db->get_results($sql);
}
//用户头像上传
function process_picture($fileName,$oldFile='')
{
	
	if(!empty($_FILES[$fileName]))
	{
		del_old_file($oldFile);
		require_once(ABSPATH."/inc/class.upload.php");
		$upload = new Upload();
		$upload->AllowExt='jpg|jpeg|gif|bmp|png';
		$fileName = $upload->SaveFile($fileName);
		if(empty($fileName))echo $upload->showError();
		require_once(ABSPATH."/inc/class.paint.php");
		$paint = new Paint(UPLOADPATH.$fileName);
		$newname=$paint->Resize(moduleUserWidth,moduleUserHight,'s_');
		@unlink(ABSPATH.UPLOADPATH.$fileName);
		return $newname;
	}else{
		return '';
	}
}
function del_old_file($oldFile)
{
	if(!empty($oldFile))
	{
		if(is_file(ABSPATH.$oldFile))
		{
			@unlink(ABSPATH.$oldFile);
		}
	}
}
?>