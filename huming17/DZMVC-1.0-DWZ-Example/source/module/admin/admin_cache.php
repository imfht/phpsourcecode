<?php

/**
 * 信息管理
 * @author HumingXu E-mail:huming17@126.com
 */
switch ($do) {
	case "update":
	    $is_submit = isset($_REQUEST['is_submit']) ? $_REQUEST['is_submit'] : '';
		if($is_submit){
			//DEBUG 执行更新缓存
			//updatecache();
			$cache_name = isset($_REQUEST['cache_name']) ? $_REQUEST['cache_name'] : '';
			updatecache($cache_name);
			echo '{
			    "statusCode":"200",
			    "message":"操作成功",
			    "navTabId":"",
			    "rel":"",
			    "callbackType":"forward",
			    "forwardUrl":"admin.php?mod=cache&action=index",
			    "confirmMsg":""
			}';
		}
		break;
	
	default:
        include template('admin/cache/cache');
        break;
}
?>