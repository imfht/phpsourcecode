<?php
/*菜单项*/
$menus=array();
$subs=array();
$menuRoot=$db->get_results("SELECT * FROM ".TB_PREFIX."menu WHERE dtLanguage = '".$_SESSION[TB_PREFIX.'doclang']."' ORDER BY ordering ASC",ARRAY_A);
if(!empty($menuRoot))
{
   foreach($menuRoot as $menu){
	  $menus[$menu['id']]=$menu; //重构菜单数组;
	  $subs[$menu['parentId']][]=$menu['id'];//频道子类数组;		  
   }	
}
/****/
$tempId = getMenu_info('type')=='product'?getMenu_info('id',true):$request['p'];
$customs = $db->get_row('SELECT * FROM `'.TB_PREFIX.'models_set` WHERE channelId = '.$tempId,ARRAY_A);
$noComment=array('guestbook', 'jobs', 'linkers', 'webmap', 'order', 'user', 'rss');
//自定义表单 输出标签
function sys_push($value='',$style='<p>{name}:{content}</p>',$tab=0,$coo='<|@|>')
{
	global $db,$tag,$customs;
	$rs = $customs;
	if(!empty($rs['field'])||!empty($rs['field_tab']))
	{
		$fields  = explode('@',$rs['field']);
		$tabs 	 = explode('@',$rs['field_tab']);
		
		$data 	 = explode($coo,$value);

		if(!$tab)
		{
			for($s=0;$s<count($fields);$s++)
			{
				$palce = array('{name}'=>$fields[$s],'{value}'=>$data[$s]);
				$push = strtr($style,$palce);
				
				echo $push;
			}
		}
		else
		{			
			for($s=0;$s<count($tabs);$s++)
			{
				$palce = array('{name}'=>$tabs[$s],'{value}'=>ewebeditor(EDITORSTYLE,'content['.$s.']',$data[$s],'content'.$s));
				$push = strtr($style,$palce);
						
				echo $push;
			}
		}
	}
}
//系统URL路由器  by grysoft
function sys_href($channelId=0,$type='article',$id=0,$action=0){
	global $db;
	if($type=='user'){
		$rs = $db->get_row('SELECT * FROM '.TB_PREFIX.'menu WHERE type ="user" LIMIT 1');
		if($rs)
		$channelId = $rs->id;
		else
		exit('对不起。您尚未创建会员频道，会员功能暂不可用。');	 
	}
	$isurlrewrite = true;
	$menuName=getMenu_info('menuName',false,$channelId);
	switch ($type){
		case 'article':       //图片模块、频道栏目链接
			$link = $isurlrewrite?'/'.$menuName.'/':'./index.php?p='.$channelId;
			break;
		case 'guestbook':    //留言模块
			$link = $isurlrewrite?'/'.$menuName.'/guestbook_'.$id.'.html':'./index.php?p='.$channelId.'&a=view&r='.$id;
			break;
		case 'list':        //文章列表
			$link = $isurlrewrite?'/'.$menuName.'/n'.$id.'.html':'./index.php?p='.$channelId.'&a=view&r='.$id;
			break;
		case 'download':    //下载模块
			$link = $isurlrewrite?'/'.$menuName.'/action_download_'.$id.'.html':'./index.php?p='.$channelId.'&a=download&r='.$id;
			break;
		case 'view':         //view
			$link = $isurlrewrite?'/'.$menuName.'/v'.$id.'.html':'./index.php?p='.$channelId.'&a=view&r='.$id;
			break;
		case 'video':         //view
			$link = $isurlrewrite?'/'.$menuName.'/video'.$id.'.html':'./index.php?p='.$channelId.'&a=view&r='.$id;
			break; 
		case 'jobs':    //招聘模块 提交页
			$link = $isurlrewrite?'/'.$menuName.'/jobs_'.$id.'.html':'./index.php?p='.$channelId.'&a=view&r='.$id;
			break;
		case 'picture':       //图片模块
			$link = $isurlrewrite?'/'.$menuName.'/pic_'.$id.'.html':'./index.php?p='.$channelId.'&a=view&r='.$id;
			break;
		case 'poll':       //投票模块
			$link = $isurlrewrite?'/'.$menuName.'/poll_'.$id.'.html':'./index.php?p='.$channelId.'&a=view&r='.$id;
			break;
		case 'product':         //产品模块
			$link = $isurlrewrite?'/'.$menuName.'/product_'.$id.'.html':'./index.php?p='.$channelId.'&a=view&r='.$id;
			break;
		case 'rss':    //RSS
			$link = $isurlrewrite?'/'.$menuName.'/rss_'.$id.'_'.$action.'.html':'./index.php?p='.$channelId.'&a=get_rss&r='.$id.'$i='.$action;
			break;
		default:
		$link = $isurlrewrite?'/'.$menuName.'/'.$type.'.html':'./index.php?p='.$channelId.'&a='.$type;
	}
	return $link;
}
function getLocation($str=' →'){
	global $db,$request;
	if(!empty($request['p']))
	{
		$sql="SELECT id, parentId, deep, title FROM `".TB_PREFIX."menu`  ORDER BY deep ASC";
		$menus=$db->get_results($sql);
		$temp_str 	= ' <a href="./index.php">首 页</a>';
		foreach(trace_parentnodes($request['p'],$menus) as $menu){
				$temp_str   .= $str.' <a href="./index.php?p='.$menu->id.'">'.$menu->title.'</a>';
		}
	}
	else 
	{
		return ' 首 页 ';
	}
	return $temp_str;
}
function trace_parentnodes($parentId,$menus){
	if(!$menus)return array();
	foreach($menus as $o)
	{
		if($o->id == $parentId)
		{
			if($o->deep){
				$arr=trace_parentnodes($o->parentId,$menus);
			}
			$arr[]=$o;
		}
	}
	return $arr;
}
function getMenu_info($field='title',$ischannel=false,$id=0){
	global $request,$menus;
	$id=empty($id)?$request['p']:$id;		
	if($ischannel)
	{		
		if($menus[$id]['deep']!=0)
		{
			do
			{	  
			 	$id=$menus[$id]['parentId'];
			}while($menus[$id]['deep']!=0);			
		}		
		return $menus[$id][$field];
	}
	else
	{
	  return $menus[$id][$field];	
	}
}
function isweb($web){
	$url_this=$_SERVER["HTTP_HOST"];
	$ary = explode('.',$url_this);
	$web = explode('|',$web);
	for($i=0;$i<count($web);$i++)
	{
		if(in_array($web[$i],$ary)){
			return ;
		}
	}
	exit;
}
//function by grysoft 
//
//模块字段自定义设置
function setModels()
{
	global $db,$request;
	
	$fields  = !empty($request['fields'])?@implode('@',array_filter($request['fields'])):'';
	$tabs    = !empty($request['tabs'])?@implode('@',array_filter($request['tabs'])):'';
	$type = getMenu_info('type',false,$request['p']);
	$id = $type=='product'?getMenu_info('id',true,$request['p']):$request['p'];
	$rs = $db->get_row("SELECT * FROM `".TB_PREFIX."models_set` WHERE channelId = ".$id);
	if(empty($rs))
	{
		$sql="INSERT INTO `".TB_PREFIX."models_set` (`channelId`, `type`, `field`, `field_tab`) VALUES ('".$id."', '".$type."', '".$fields."', '".$tabs."')";	
	}
	else
	{
		$sql="UPDATE  `".TB_PREFIX."models_set` SET  `field` ='".$fields."' , `field_tab` = '".$tabs."' WHERE channelId = ".$id;
	}
	$db->query($sql);
	redirect_to($request['p'],'index');	
}
/* 搜索引擎Ping插件  grysoft(狗头巫师)*/
function docPing($channelId, $id, $type='' )
{
	global $db,$module_name;
	
	if(DOCPING!==true)return;
	
	$type= $type?$type:$module_name;
    $xml = '<?xml version="1.0" encoding="UTF-8"?> 
<methodCall> 
<methodName>weblogUpdates.extendedPing</methodName> 
<params> 
<param><value><string>' . SITENAME . '</string></value></param> 
<param><value><string>http://' . WEBURL . '</string></value></param> 
<param><value><string>http://' . WEBURL . sys_href($channelId, $type, $id) . '</string></value></param> 
<param><value><string>http://' . WEBURL . '?m=rss&a=get_rss&r=' . $channelId . '</string></value></param> 
</params> 
</methodCall>';

$opts = array(
  'http'=>array(
    'method'=>"POST",
    'header'=>"http://ping.baidu.com/ping/RPC2 HTTP/1.0\r\n" .
              "Content-type: text/xml\r\n" .
			  "Content-length: " . strlen($xml),
	'content'=>"mypost=".$xml
  )
);
$context = stream_context_create($opts);
$file = @file_get_contents('http://ping.baidu.com/ping/RPC2', false, $context);
}
///////////////////////////////////////////////////////
function update_tip(){
	if(isset($_SESSION['satict'])){
		$_SESSION['satict']+=1;
	}else{
		$_SESSION['satict']=1;
	}
	$versioninfo=explode('.',VERSION);
	//版本格式  **.**.******
	if(strlen(VERSION)<12){
		if(strlen($versioninfo[0])<2){
			$versioninfo[0]='0'.$versioninfo[0];
		}
		if(strlen($versioninfo[1])<2){
			$versioninfo[1]=$versioninfo[1].'0';
		}
		//更新日期不做处理  目前官方提供的发布日期格式一致 均为六位 010203
		$version=$versioninfo[0].'.'.$versioninfo[1].'.'.$versioninfo[2];
		
	}elseif(strlen(VERSION)==12){
		$version=VERSION;
	}else{
		$version='99.99.999999';
	}
	return 
		  '<script language="JavaScript">var localVersion="'.$version.'";var satict='.$_SESSION['satict'].';</script>'
		  .'<script language="JavaScript" type="text/javascript" src="http://www.doccms.com/docupgrade/update_tip.js?v='.VERSION.'"></script>';
}
?>