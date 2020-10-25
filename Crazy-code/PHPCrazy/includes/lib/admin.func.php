<?php
/*
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

////////////////////// 后台相关 /////////////////////


function AdminUrl($append) {

	return HomeUrl(ADMIN_PATH.'/' . $append);

}

function AdminActionUrl($action) {

    return AdminUrl('admin.php?action='.$action);
}
?>