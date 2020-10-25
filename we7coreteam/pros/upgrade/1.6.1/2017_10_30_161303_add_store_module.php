<?php

namespace We7\V161;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1509351183
 * @version 1.6.1
 */

class AddStoreModule {

	/**
	 *  执行更新
	 */
	public function up() {
		$module_exist = pdo_get('modules', array('name' => 'store'));
		if (empty($module_exist)) {
			$data = array(
				'name' => 'store',
				'type' => 'business',
				'title' => '站内商城',
				'title_initial' => 'Z',
				'version' => '1.0',
				'ability' => '站内商城',
				'description' => '站内商城',
				'author' => 'we7',
				'issystem' => '1',
				'wxapp_support' => '1',
				'app_support' => '2',
			);
			pdo_insert('modules', $data);
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		