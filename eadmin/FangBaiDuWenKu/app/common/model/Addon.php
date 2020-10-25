<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
 * 插件模型
 */
class Addon extends ModelBase
{
	/**
	 * 获取插件的后台列表
	 */
	public function getAdminList(){
		$admin = array();
		$db_addons = $this->where("status=1 AND has_adminlist=1")->select();
		$menu_list=array();
		if($db_addons){
		        $menu_list['href']='javascript:;';
            	$menu_list['title']=	'插件列表';
            	$menu_list['icon']=	'fa-microchip';
            	
            	$menu_list['spread']=false;
            	
            	
	    	foreach($db_addons as $key=> $vo){
       	
       		
       		$menu_list['children'][$key]['href']=url("Addon/adminlist",array('name'=>$vo['name']));
       		$menu_list['children'][$key]['title']=	$vo['title'];
            $menu_list['children'][$key]['icon']=	'fa-dot-circle-o';
       		
       	   }
		
		
	  }
		return $menu_list;
	} 
}
