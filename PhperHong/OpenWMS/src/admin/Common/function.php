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
	function router_status($status){
		if ($status == '-1'){
			return 'infobox-red';
		}elseif($status == '1'){
			return 'infobox-green';
		}elseif($status == '2'){
			return 'infobox-grey';
		}elseif($status == '3'){
			return 'infobox-orange';
		}elseif($status == '4'){
			return 'infobox-wood';
		}
	}
	function router_status1($status){
		if ($status == '-1'){
			return '<font color="red">未连接</font>';
		}elseif($status == '1'){
			return '<font color="#8EE67A">在线</font>'; 
		}elseif($status == '2'){
			return '<font color="#FFD850">离线</font>';
		}elseif($status == '3'){
			return '<font color="#feb902">已关闭认证</font>';
		}elseif($status == '4'){
			return '<font color="#feb902">未绑定</font>';
		}
	}
	function account_type($type, $date){
		$data = array('永久账号', '月收费账号', '开放注册账号');
		$str = '';
		$reg_date = C('REG_DATE');
		if ($type == 1){
			$deadline = strtotime($date);
			if ($deadline< time()){
				$str = '(<font color="red">已过期</font>)';
			}
		}else if ($type == 2 && $reg_date > 0){
			$deadline = strtotime($date);
			if ($deadline< time()){
				$str = '(<font color="red">已过期</font>)';
			}
		}
		return $data[$type].$str;
	}
	function industry_type($id){
		$INDUSTRY = C('INDUSTRY');
		return $INDUSTRY[$id];
	}
	function href($type, $website){
		$data = array('website'=>'跳转到微站', 'sourcewebsite'=>'跳转请求网页', 'fixedwebsite'=>'跳转指定网页', 'not'=>'不跳转');
		$str = '';
		if ($type == 'fixedwebsite'){
			$str = '('.$website.')';
		}
		return $data[$type].$str;

	}
	function export(){
		$backup         = D('Backup');
        $backup->export();
	}
?>