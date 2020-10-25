<?php
//评论模块
function isComments()
{
	global $db;
	global $params;
	global $tag;	// 标签数组
	global $request;
	
	$sql = "SELECT isComment FROM ".TB_PREFIX."menu WHERE id=".$params['id'];
	$isComment = $db->get_var($sql);
	if(($isComment == '1' && $params['action'] == 'view') || ($isComment == '1' && ($params['model'] == 'article' ||$params['model'] == 'mapshow') ))
	{
		$sql="SELECT a.*,b.nickname FROM ".TB_PREFIX."comment a left join  ".TB_PREFIX."user b on a.memberId=b.id WHERE a.channelId=".$params['id']." and a.recordId=".$params['args'];
		
		$username=isset($_SESSION[TB_PREFIX.'user']) ? $_SESSION[TB_PREFIX.'user'] : '';
		$userlevel=isset($_SESSION[TB_PREFIX.'admin_roleId']) ? $_SESSION[TB_PREFIX.'admin_roleId'] : '';
		
		if(COMMENTAUDITING)
		{
			if(!empty($username) || $userlevel>=8 ){ }else{	$sql.=" and a.auditing=1 ";  }
		}
		
		$results = $db->get_results($sql);
        $counts = count($results);

		$part_path = ABSPATH.'/skins/'.STYLENAME.'/parts/comment/comment_index.php';
		if(is_file($part_path))
		require($part_path);
		else 
		echo '<span style="color:RED"><strong>加载 /skins/'.STYLENAME.'/parts/comment/comment_index.php 样式资源文件失败，程序意外终止。</strong></span>';
	}
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
	return $arr?$arr:array();
}

function get_subMenu_name()
{
	global $db,$params;
	$sql = "SELECT title FROM ".TB_PREFIX."menu WHERE id=".$params['id'];
	if(URLREWRITE)
	return '<a href="/">'.$db->get_var($sql).'</a>';
	else
	return '<a href="/?p='.$params['id'].'">'.$db->get_var($sql).'</a>';
}
function getIdByFile($file)
{
	global $db;
	$sql="SELECT id FROM ".TB_PREFIX."menu WHERE menuName='$file'";
	return $db->get_var($sql);
}
function get_menuName($id)
{
	global $db;
	if($id==0)
	return 'list';
	else
	return $db->get_var("SELECT menuName FROM ".TB_PREFIX."menu WHERE id=$id");
}
function get_channel_name($id)
{
	global $db;
	if($id)
	{
		$result=(array)$db->get_row("SELECT * FROM ".TB_PREFIX."menu WHERE id=$id");
		return $result['title'];
	}
	else
	return null;
}