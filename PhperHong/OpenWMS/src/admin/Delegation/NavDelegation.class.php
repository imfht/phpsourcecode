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
class NavDelegation {
	public function onAddCell($args){
		$str = '' ;
		
		
		if($args[2]['name'] == 'type') {
		
			$data = array('自定义导航', '系统导航');
			$str = $data[$args[1]['type']];
		}else if($args[2]['name'] == 'nav_image'){
			
            $str = '<i class="'.$args[1]['nav_image'].'"></i>';
		}else if ($args[2]['name'] == 'status'){
			$data = array('<span style="color:red;">禁用</span>', '<span style="color:green;">启用</span>');
			$str = $data[$args[1]['status']];

		}else {
			return '' ;
		}
		return "<td>{$str}</td>" ;
	}
	public function onAddOperation($args){
		if ($args[1]['type'] == 1 && $args[2]['name'] == 'del'){
			return '&nbsp;';
		}
		
	}
}