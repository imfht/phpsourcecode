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
class ProductDelegation {
	public function onAddCell($args){
		$str = '' ;
		
		
		if($args[2]['name'] == 'thumb') {
			if (!empty($args[1]['thumb'])){
				$str = '<img src="'.__ROOT__ .'/upload/station_product/'.$args[1]['thumb'].'" width="50" height="50">';
			}else{
				$str = '';
			}
			
		}else {
			return '' ;
		}
		return "<td>{$str}</td>" ;
	}
	
}