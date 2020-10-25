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
class UserDelegation {
	public function onAddCell($args){
		$str = '' ;
		
		
		if($args[2]['name'] == 'username') {
			$str = user_name($args[1]['auth_type'], $args[1]['username']);
		}else if($args[2]['name'] == 'auth_type'){
            $str = auth_typeFiler($args[1]['auth_type']);
		}else if($args[2]['name'] == 'online_time'){
			$str = secondesToDay($args[1]['online_time']);
		}else if ($args[2]['name'] == 'outgoing'){
			
			$temp = '<div style="text-align: left;"><i class="icon-long-arrow-up red"></i>&nbsp;'.Bytes($args[1]['outgoing'], 'KB').'</div>';
			$temp .= '<div style="text-align: left;"><i  class="icon-long-arrow-down green"></i>&nbsp;'.Bytes($args[1]['incoming'], 'KB').'</div>';
			$str = $temp;
		}else if ($args[2]['name'] == 'device_type'){
            $str = device_typeFiler($args[1]['device_type']);
		}else if ($args[2]['name'] == 'type'){
			if ($args[1]['type'] == 2){
                $str = '营销短信';
            }
            $str = '验证码短信';
		}else if ($args[2]['name'] == 'deviceType'){
			$str = device_typeFiler($args[1]['deviceType']);
		}else {
			return '' ;
		}
		return "<td>{$str}</td>" ;
	}
	
}