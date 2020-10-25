<?php
function index()
{
	global $db;
	global $params;
	global $tag;	// 标签数组

	$sql="SELECT * FROM ".TB_PREFIX."menu  WHERE isHidden=0 AND isExternalLinks=0 AND type NOT IN ('rss','webmap') ORDER BY ordering  ASC";	
	$tempmenus=$db->get_results($sql,ARRAY_A);
	if(!empty($tempmenus))
	{
		foreach($tempmenus as $menu)
		{
			$menus[$menu['id']]=$menu;
		}
		unset($tempmenus);
		$stack = array(0);
		while($stack) 
		{
			$parentid = array_pop($stack);
			if($parentid)
			{
				$tag['data.results'][]=$menus[$parentid];
			}
			$substack = array();      
			foreach($menus as $id=>$menu)
			{            
				if($menu['parentId']==$parentid)
				{                 
				  	$menus[$id]['prefix'] = $menus[$parentid]['prefix'].'&nbsp;&nbsp;&nbsp;&nbsp;';                        
				  	array_push($substack,$id);   
				}      
			}      
			$substack = array_reverse($substack);      
			$stack = array_merge($stack,$substack);
		}
		unset($menus);
	}
	else
	{
		unset($tempmenus);
	}
}
function get_rss(){
	global $db,$params;
	header('Content-Type: text/xml; charset=utf-8');
	require(ABSPATH.'/inc/class.doc_rss.php');
	$shlrss= new doc_rss($db,$params['args'],$params['i']); 
	echo $shlrss->get_rss();
	$shlrss=null;
	exit;
}
?>