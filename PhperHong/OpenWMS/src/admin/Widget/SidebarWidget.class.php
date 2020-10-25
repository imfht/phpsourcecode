<?php
// +----------------------------------------------------------------------
// | openWMS (开源wifi营销平台)
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2025 http://cnrouter.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.gnu.org/licenses/gpl-2.0.html )
// +----------------------------------------------------------------------
// | Author: PhperHong <phperhong@cnrouter.com>
// +----------------------------------------------------------------------
namespace admin\Widget;
use Think\Controller;
/*
* 后台首页菜单
*/
class SidebarWidget extends Controller {
    public function menu($data){
    	//$cookielog = 
    	//获取当前控制器
    	$m = CONTROLLER_NAME;
    	$a = ACTION_NAME;

    	$admin = D('Admin');
        $menu_list = $admin->get_menu_list_by_groupid();

        $temp = $this->set_class($menu_list, $m, $a);

        //如果当前控制器不存在菜单中，则取历史控制器

        if (!$temp){
        	$m = cookie('MODULE_NAME');  
        	$a = cookie('ACTION_NAME');
        	$temp = $this->set_class($menu_list, $m, $a);

        }
        if ($temp){
            $menu_list = $temp;
        	//更新记录
        	cookie('MODULE_NAME', $m);  
        	cookie('ACTION_NAME', $a);  
        }

		$this->assign('name', $menu_list);
		$this->display('Widget/siderbar');
   }
   	private function set_class($menu_list, $m, $a){

   		$b = false;
   		//判断当前控制器是否在菜单中
        foreach ($menu_list as $key => &$value) {
        	$temp1 = $value['url'];

        	$temp = explode('/', $temp1);

            if ($m == $temp[0] && $a == $temp[1]){
        		$value['class'] = 'active open';
        		$b = true;
        	}
        	if ($value['sub']){
        		foreach ($value['sub'] as $k => &$v) {
        			$temp1 = $v['url'];
		        	$temp = explode('/', $temp1);
		        	if ($m == $temp[0] && $a == $temp[1]){
		        		$value['class'] = 'active open';
		        		$v['class'] = 'active';
		        		$b = true;
		        	}
        		}
        	}
        }
        if (!$b){

        	return false;
        }
        return $menu_list;
   	}
   
 }