<?php

if(!defined('IN_SYSTEM')) {
	exit('Access Denied');
}

class helper_access {

	public static function check_module($module) {
		$status = 0;
		$allowfuntype = array('portal', 'group', 'follow', 'collection', 'guide', 'feed', 'blog', 'doing', 'album', 'share', 'wall', 'homepage', 'ranklist');
		$module = in_array($module, $allowfuntype) ? trim($module) : '';
		if(!empty($module)) {
			$status = getglobal('setting/'.$module.'status');
		}
		return $status;
	}
}

?>