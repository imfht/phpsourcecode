<?php

namespace We7\V163;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1510988584
 * @version 1.6.3
 */

class CreateCloudRecycleModule {

	/**
	 *  执行更新
	 */
	public function up() {
		load()->object('cloudapi');
		$recycle_module = pdo_getall('modules_recycle', array(), array('modulename'), 'modulename');
		$cloudapi = new \CloudApi();
		$cloudapi->post('cache', 'set', array('key' => cache_system_key('recycle_module:'), 'value' => $recycle_module));
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		