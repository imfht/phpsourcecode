<?php

namespace We7\V170;

defined('IN_IA') or exit('Access Denied');

class AddAppMenu {

	/**
	 *  执行更新
	 */
	public function up() {
		$app_menu = pdo_get('core_menu', array('group_name' => 'frame', 'is_system' => 1, 'permission_name' => 'phoneapp'));
		if (empty($app_menu)) {
			pdo_insert('core_menu', array('group_name' => 'frame', 'is_display' => 0, 'is_system' => 1, 'permission_name' => 'phoneapp'));
			cache_delete('system_frame');
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		