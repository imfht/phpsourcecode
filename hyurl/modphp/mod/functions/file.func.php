<?php
/** 在更新和删除文件时检查管理员或编辑权限 */
add_hook(array('file.update.check_permission', 'file.delete.check_permission'), function(){
	if(!is_logined())
		return error(lang('user.notLoggedIn'));
	if(!is_editor() && !is_admin())
		return error(lang('mod.permissionDenied'));
}, false);

/** 获取文件路径为绝对路径 */
add_hook('file.get.absolute_src', function($data){
	if(!path_starts_with($data['file_src'], site_url())){
		$data['file_src'] = site_url().$data['file_src'];
		return $data;
	}
}, false);

/** 永远使用相对路径获取文件（如果存在） */
add_hook('file.get.before.relative_src', function($arg){
	if(!empty($arg['file_src']) && path_starts_with($arg['file_src'], site_url())){
		$arg['file_src'] = substr($arg['file_src'], strlen(site_url()));
		return $arg;
	}
}, false);