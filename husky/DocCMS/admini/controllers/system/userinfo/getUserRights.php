<?php
/**
 * 管理者的权限信息
 */
if($_SESSION[TB_PREFIX.'admin_roleId']<10){
		die("warn:Access Forbidden");
}		
global $request, $db,$tmp;
	$tmp['id']=abs(intval($request['id']));
	//管理者信息
	$tmp['sql']='SELECT * FROM '.TB_PREFIX.'user WHERE id='.$tmp['id'];
	$tmp['user']=$db->get_row($tmp['sql']);
	$path=ABSPATH.'/admini/controllers/system/userinfo/config/';//目录路径
	$filename=$path.'/dt-RightsManagement-config-'.$tmp['user']->id.'.php';//文件全路径
	if(!is_file($filename)){
			if($tmp['user']->role>8){
				$sql="SELECT *  FROM ".TB_PREFIX."menu  order by  ordering  asc";
			}else{	
				echo $tmp['menuinfo']='此用户暂未分配管理栏目';
				exit;	
			}
	}else{
		include($filename);
		$tmp['MUNE_ID_ARRAY']=unserialize(MUNE_ID_ARRAY);
		for($i=0;$i<count($tmp['MUNE_ID_ARRAY']);$i++)
		{
			if($tmp['MUNE_ID_ARRAY'][$i]=='flash')
			{
			   $tmp['MUNE_ID_ARRAY'][$i]='';
			   $isflash = true;
			}
		}
		
		if(!$tmp['MUNE_ID_ARRAY'][0]){
			echo $tmp['menuinfo']='此用户暂未分配管理栏目';
			exit;	
		}else{
			$sql="SELECT *  FROM ".TB_PREFIX."menu a WHERE id in (".(implode(',',array_filter($tmp['MUNE_ID_ARRAY']))).") order by  ordering  asc";
		}
	}
	
	//频道栏目信息
			$tempmenus=$db->get_results($sql,ARRAY_A);
			if(!empty($tempmenus))
			{
				foreach($tempmenus as $menu){
					$menus[$menu['id']]=$menu;
				}
				$stack = array(0);
				while($stack) {
				  	$parentid = array_pop($stack); 
				  	if($parentid) {
				  		$tmp['menuinfo'].='<li>'."\r\n";
				  		$tmp['menuinfo'].='<span class="tree"><span class="prefix">'.$menus[$parentid]['prefix'].'</span><span class="title">'.$menus[$parentid]['title'].'</span></span>'."\r\n";
				  		$tmp['menuinfo'].='<span class="menuname">'.$menus[$parentid]['menuName'].'</span>'."\r\n";
				  		$tmp['menuinfo'].='<span class="type">'.$menus[$parentid]['type'].'</span>'."\r\n";
				  		$tmp['menuinfo'].='<span  class="check"><input type="checkbox" disabled checked="checked"></span>'."\r\n";
				  		$tmp['menuinfo'].='</li>'."\r\n";
				  	}
				  	$substack = array();      
				  	foreach($menus as $id=>$menu) {            
				  		if($menu['parentId']==$parentid) {                 
				  			$menus[$id]['prefix'] = $menus[$parentid]['prefix'].'&nbsp;&nbsp;&nbsp;&nbsp;';                        
				  			array_push($substack,$id);   
				  		}      
				  	}      
				  	$substack = array_reverse($substack);      
				  	$stack = array_merge($stack,$substack);
				}
				if($isflash)
				{
					$tmp['menuinfo'].='<li>'."\r\n";
					$tmp['menuinfo'].='<span class="tree"><span class="prefix">&nbsp;&nbsp;&nbsp;&nbsp;</span><span class="title">系统广告管理</span></span>'."\r\n";
					$tmp['menuinfo'].='<span class="menuname">flash</span>'."\r\n";
					$tmp['menuinfo'].='<span class="type">flash</span>'."\r\n";
					$tmp['menuinfo'].='<span  class="check"><input type="checkbox" disabled checked="checked"></span>'."\r\n";
					$tmp['menuinfo'].='</li>'."\r\n";
				}
			}else{
				$tmp['menuinfo']='请创建频道栏目！';
			}
?>