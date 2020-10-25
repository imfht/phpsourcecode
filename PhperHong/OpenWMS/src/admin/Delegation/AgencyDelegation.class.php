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
class AgencyDelegation {
	public function onAddCell($args){
		$str = '' ;
		
		
		if($args[2]['name'] == 'allow_modify') {
			$str = $args[1]['use_modify'].'/'.$args[1]['allow_modify'];
		}else if($args[2]['name'] == 'merchant_limit'){
            $str = $args[1]['use_merchant'].'/'.$args[1]['merchant_limit'];
		}else {
			return '' ;
		}
		return "<td>{$str}</td>" ;
	}
	
}