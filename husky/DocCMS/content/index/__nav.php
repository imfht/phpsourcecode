<?php
/**
 * DOCCMS 菜单 nav 标签
 * @author: grysoft    QQ:767912290
 * @copyright DOCCMS
 */
function nav_main($style=0,$n=0)
{
	global $db,$menuRoot,$tag,$subs;
	if(!empty($menuRoot))
	{
		$i=0;//初始化递增变量 $i
		foreach($menuRoot as $data)
		{
			if(!$data['deep'] && !$data['isHidden'])
			{	
			    //获取菜单地址
			    if($data['isExternalLinks'])
					$url = $data['redirectUrl'];
				elseif(URLREWRITE)
					$url = '/'.$data['menuName'].'/';
				else
					$url ='?p='.$data['id'];	
				//引入样式文件					
				require(get_style_file('__nav','nav_main',$style));					 		
				$i++; //变量递增 
			}
			if($n && $n==$i)break;
		}							
	}
}
function nav_sub($id=0,$style=0,$expand=1,$n=0)
{	
	global $menus,$subs,$params,$db,$tag;    

	$id=!$id?sys_menu_info('id',true):$id;  //显示所有菜单
 				
	if(!isset($subs[$id])) return; //没有子类,返回空;
				   
	$i=0; //初始化递增变量 $i	
	foreach($subs[$id] as $sid){
		$data = $menus[$sid];  //统一模板中的调用标记，方便模板的制作。
		if(!$data['isHidden'])
		{
			//处理图片路径
			if(!empty($data['originalPic']))
			{
				$data['originalPic']=ispic($data['originalPic']);
				$data['smallPic']=ispic($data['smallPic']);
			}
			$str='';//重置
			if($expand)
			{
				for($s=0;$s<$menus[$sid]['deep']-1;$s++)$str.='&nbsp;&nbsp;&nbsp;&nbsp;';
				$pic = '<img src="'.$tag['path.root'].'/inc/img/nav/expand-0.gif" border="0" align="absmiddle"/>';
				$pic = isset($subs[$data['id']])&&!$expand?$pic:''; 
			}
			//获取栏目地址
			if($data['isExternalLinks'])  
				$url = $data['redirectUrl'];
			elseif(URLREWRITE)
				$url = '/'.$data['menuName'].'/';
			else
				$url ='?p='.$data['id'];	
				
			$data['title'] = $str.$data['title'].$pic; //生成title
			//引入样式文件
			require(get_style_file('__nav','nav_sub',$style));
			if($expand == 1 )
			{
				nav_sub($sid,$style,$expand);  //递归
			}	
			if($expand == 2 )
			{
				if($menus[$sid]['id'] == $params['id'] || $menus[$params['id']]['parentId'] == $data['id'])
				nav_sub($sid,$style,$expand);  //递归
			}

			$str='';
			$i++;    //变量递增  
		}
		if($n && $n==$i)break;		
	} 
}
function nav_custom($ids='',$style=0)
{
	global $db,$tag;
	if(empty($ids))
	{
		nav_main($style);
	}
	else
	{
		$tempId = explode(',',$ids);
		for($i=0;$i<count($tempId);$i++)
		{
			$tempId[$i] = intval($tempId[$i]);
			$data['id']=$tempId[$i];
			$data['title']       	 = sys_menu_info('title',false,$tempId[$i])?sys_menu_info('title',false,$tempId[$i]):'Null';
			$data['menuName']		 = sys_menu_info('menuName',false,$tempId[$i]);
			$data['keyword']	     = sys_menu_info('keyword',false,$tempId[$i]);
			$data['type']	         = sys_menu_info('type',false,$tempId[$i]);
			$data['description']	 = sys_menu_info('description',false,$tempId[$i]);
			$data['isExternalLinks'] = sys_menu_info('isExternalLinks',false,$tempId[$i]);
			$data['smallPic']        = sys_menu_info('smallPic',false,$tempId[$i]);
			$data['originalPic']     = sys_menu_info('originalPic',false,$tempId[$i]);
			$data['redirectUrl']     = sys_menu_info('redirectUrl',false,$tempId[$i]);
			
			if($data['isExternalLinks'])  
				$url = $data['redirectUrl'];
			elseif(URLREWRITE)
				$url = '/'.$data['menuName'].'/';
			else
				$url ='?p='.$data['id'];	
				
			require(get_style_file('__nav','nav_custom',$style));
		}
	}
}
function nav_location($str='>>',$home='首页')
{
	global $db,$menu_arr,$params,$tag;
	if(!empty($menu_arr['id']))
	{
		$sql="SELECT id, parentId, deep, menuName, title, isExternalLinks FROM `".TB_PREFIX."menu`  ORDER BY deep ASC";
		$menus=$db->get_results($sql);
		$temp_str 	= '<a href="/'.$tag['path.root'].'">'.$home.'</a>';
		foreach(trace_parent_nodes($menu_arr['id'],$menus) as $menu){
			if($menu->isExternalLinks)
			{
				$temp_str   .= $str.'<a href="'.$menu->redirectUrl.'" target="_blank">'.$menu->title.'</a>';
			}
			else
			{
				$temp_str   .= $str.'<a href="'.sys_href($menu->id).'">'.$menu->title.'</a>';
			}
		}
	}
	echo $temp_str;
}