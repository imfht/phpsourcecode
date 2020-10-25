<?php
function shl_webmap($channelId,$style=0)
{
	if(!is_int($channelId))return ('parameters $channelId is not integer in doc_webmap()!');
	if(!is_int($style))return ('parameters $style is not integer in doc_webmap()!');
	global $db,$tag;
	$sql="SELECT * FROM ".TB_PREFIX."menu WHERE isHidden=0  ORDER BY ordering ASC";	
	$tempmenus=$db->get_results($sql,ARRAY_A);
	if(!empty($tempmenus))
	{
		foreach($tempmenus as $menu)
		{
			$menus[$menu['id']]=$menu;
		}
		unset($tempmenus);
		$stack = array($channelId);
		while($stack) {
			$parentid = array_pop($stack); 
			if($parentid)
			{
				$o=(object)$menus[$parentid];
				$data=(array)$o;
				require(get_style_file('webmap','webmap',$style));
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
	}else{ echo '暂无数据！';}
}
function doc_webmap($channelId=0,$style=0)
{
	shl_webmap($channelId,$style);
}
?>