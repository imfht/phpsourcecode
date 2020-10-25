<?php

namespace We7\V169;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1516615793
 * @version 1.6.9
 */

class UpdateAttchmentGroupid {

	/**
	 *  执行更新
	 */
	public function up() {
		if(pdo_fieldexists('core_attachment', 'group_id')) {
			$tableName = tablename('core_attachment');
			$sql = "UPDATE $tableName set group_id = 0 where group_id is NULL";
			pdo_query($sql);
		}

		if(pdo_fieldexists('wechat_attachment', 'group_id')) {
			$tableName = tablename('wechat_attachment');
			$sql = "UPDATE $tableName set group_id = 0 where group_id is NULL";
			pdo_query($sql);
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		