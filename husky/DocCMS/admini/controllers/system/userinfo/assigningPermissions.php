<?php
/**
 * 管理者的权限信息
 */
if($_SESSION[TB_PREFIX.'admin_roleId']<10){
	    
		exit("<script>alert('非法访问：您无权查看此页面，权限分配仅有创始人用户可执行！');history.go(-1)</script>");
}
global $request, $db,$tmp;
	$tmp['id']=abs(intval($request['id']));
	//管理者信息
	$tmp['sql']='SELECT * FROM '.TB_PREFIX.'user WHERE id='.$tmp['id'];
	$tmp['user']=$db->get_row($tmp['sql']);
	$path=ABSPATH.'admini/controllers/system/userinfo/config/';//目录路径
	$filename=$path.'dt-RightsManagement-config-'.$tmp['user']->id.'.php';//文件全路径		
	if(!$_POST){
		if(!is_file($filename)){
			$tmp['MUNE_ID_ARRAY']=array(0);	
		}else{
			include($filename);
			$tmp['MUNE_ID_ARRAY']=unserialize(MUNE_ID_ARRAY);
			//频道栏目信息
		}
		$sql="SELECT *  FROM ".TB_PREFIX."menu  order by  ordering  asc";
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
			  		if(in_array($menus[$parentid]['id'],$tmp['MUNE_ID_ARRAY'])){
			  			$tmp['menuinfo'].='<span  class="check"><input id="check_'.$menus[$parentid]['id'].'" name="check[]" type="checkbox" checked="checked" value="'.$menus[$parentid]['id'].'"></span>'."\r\n";
			  		}else{
			  			$tmp['menuinfo'].='<span  class="check"><input id="check_'.$menus[$parentid]['id'].'" name="check[]" type="checkbox"  value="'.$menus[$parentid]['id'].'"></span>'."\r\n";
			  		}
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
			$tmp['menuinfo'].='<li>'."\r\n";
			$tmp['menuinfo'].='<span class="tree"><span class="prefix">&nbsp;&nbsp;&nbsp;&nbsp;</span><span class="title">系统广告管理</span></span>'."\r\n";
			$tmp['menuinfo'].='<span class="menuname">flash</span>'."\r\n";
			$tmp['menuinfo'].='<span class="type">flash</span>'."\r\n";
			if(in_array('flash',$tmp['MUNE_ID_ARRAY'])){
				$tmp['menuinfo'].='<span  class="check"><input id="check_flash" name="check[]" type="checkbox" checked="checked" value="flash"></span>'."\r\n";
			}else{
				$tmp['menuinfo'].='<span  class="check"><input id="check_flash" name="check[]" type="checkbox"  value="flash"></span>'."\r\n";
			}
			$tmp['menuinfo'].='</li>'."\r\n";
		}else{
			$tmp['menuinfo']='请先创建栏目';
		}
	}else{
		if(!empty($request['check'])){
			$tmp['menuIdArray']=$request['check'];
		}else{
			$tmp['menuIdArray']=array(0);
		}
		$tmp['config']='<?php';
		$tmp['config'].="\r";
		$tmp['config'].='/**';
		$tmp['config'].="\r";
		$tmp['config'].='* 权限分配';
		$tmp['config'].="\r";
		$tmp['config'].='* QQ：348681066';
		$tmp['config'].="\r";
		$tmp['config'].='*/';
		$tmp['config'].="\r";
		$tmp['config'].='//配置文件目录';
		$tmp['config'].="\r";
		$tmp['config'].='define(\'MUNE_ID_ARRAY\',\''.serialize($tmp['menuIdArray']).'\');';
		$tmp['config'].="\r";
		$tmp['config'].='?>';
		//createFolders($path);//创建目录
		createFile($filename,$tmp['config']);//创建配置并写	
		$tmp['config']=get_user_menus($tmp['menuIdArray']);
		createFile( $path.'/nav_'.$tmp['user']->id.'.php',$tmp['config']);//创建主频道菜单文件并写	
		unset($tmp);
		header('Location:'.$_SERVER['HTTP_REFERER']);
		exit();
	}
?>