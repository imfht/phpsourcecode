<?php
namespace We7\V157;
defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1505715277
 * @version 1.5.7
 */
class UpdateSiteStoreOrder {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_fieldexists('site_store_order', 'endtime')) {
			pdo_query('ALTER TABLE ' . tablename('site_store_order') . " ADD `endtime` INT(15) NOT NULL DEFAULT 0 ;");
		}
		$orders = pdo_getall('site_store_order');
		if (!empty($orders) && is_array($orders)) {
			foreach ($orders as $order) {
				if (!empty($order['endtime'])) {
					continue;
				}
				$endtime = strtotime('+' . $order['duration'] . ' month', $order['createtime']);
				pdo_update('site_store_order', array('endtime' => $endtime), array('id' => $order['id']));
			}
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		