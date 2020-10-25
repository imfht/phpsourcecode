<?php

namespace We7\V171;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1517817029
 * @version 1.7.1
 */

class CreateStoreCreateAccountEndtime {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_fieldexists('site_store_create_account', 'endtime')) {
			pdo_query("ALTER TABLE " . tablename('site_store_create_account') . " ADD `endtime` int(12) NOT NULL DEFAULT 0;");
		}
		$store_create_account = pdo_getall('site_store_create_account', array(), array(), 'uniacid');
		if (!empty($store_create_account)) {
			$account_list = pdo_getall('account', array('uniacid' => array_keys($store_create_account)));
			if (!empty($account_list)) {
				foreach ($account_list as $account) {
					pdo_update('site_store_create_account', array('endtime' => $account['endtime']), array('uniacid' => $account['uniacid']));
				}
			}
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		