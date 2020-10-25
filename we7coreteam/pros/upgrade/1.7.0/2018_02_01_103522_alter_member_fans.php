<?php

namespace We7\V170;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1517452522
 * @version 1.7.0
 */

class AlterMemberFans {

	/**
	 *  执行更新
	 */
	public function up() {
		$fans_table = tablename('mc_mapping_fans');
		if (!pdo_fieldexists('mc_mapping_fans', 'user_from')) {

			$sql = <<<EOT
		ALTER TABLE $fans_table ADD COLUMN `user_from` TINYINT(1) DEFAULT 0 COMMENT '1小程序0公众号';
EOT;
			pdo_query($sql);
		}
		$member_table = tablename('mc_members');
		if (!pdo_fieldexists('mc_members', 'user_from')) {

			$sql = <<<EOT
		ALTER TABLE $member_table ADD COLUMN `user_from` TINYINT(1) DEFAULT 0 COMMENT '1小程序0公众号';
EOT;
			pdo_query($sql);
		}

		if (pdo_fieldexists('mc_mapping_fans', 'user_from')) {
			$sql = "UPDATE $fans_table set user_from = 0 where user_from is NULL";
			pdo_query($sql);
		}
//
		if (pdo_fieldexists('mc_members', 'user_from')) {
			$sql = "UPDATE $member_table set user_from = 0 where user_from is NULL";
			pdo_query($sql);
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		