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
class TaskDelegation {
	public function onAddCell($args){
		$str = '' ;
		
		
		if($args[2]['name'] == 'ret') {
			
			if ($args[1]['ret'] == '-1'){
                $str = '<font color="red">执行失败</font>';
            }else if($args[1]['ret'] == '0'){
               $str = '<font color="#8EE67A">执行成功</font>'; 
            }else if($args[1]['ret'] == '2'){
                $str = '<font color="#FFD850">等待执行</font>';
            }else{
                $str = '未知';
            }
		}else if($args[2]['name'] == 'type'){
			$data = array('wifi'=>'热点名称（SSID）', 'wifidog'=>'热点配置', 'restart'=>'路由重启', 'upgrade'=>'固件升级');
            $str = $data[$args[1]['type']];
		}else {
			return '' ;
		}
		return "<td>{$str}</td>" ;
	}
	
}