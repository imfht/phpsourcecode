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
class RouterDelegation {
	public function onAddCell($args){
		$str = '' ;
		
		
		if($args[2]['name'] == 'status') {
			$str = router_status1($args[1]['status']);
		}else if($args[2]['name'] == 'online_time'){
            $str = secondesToDay($args[1]['online_time']);
		}else if ($args[2]['name'] == 'online_user_count'){
			$str = intval($args[1]['online_user_count']);
		}else {
			return '' ;
		}
		return "<td>{$str}</td>" ;
	}
	
}