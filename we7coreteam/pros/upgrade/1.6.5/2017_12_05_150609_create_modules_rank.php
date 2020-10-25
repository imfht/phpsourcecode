<?php

namespace We7\V165;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1512457569
 * @version 1.6.5
 */

class CreateModulesRank {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_tableexists('modules_rank')) {
			$sql = "CREATE TABLE IF NOT EXISTS `ims_modules_rank` (
					`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					`module_name` varchar(100) NOT NULL,
					`uid` int(10) NOT NULL,
					`rank` int(10) NOT NULL,
					PRIMARY KEY (`id`),
					KEY `module_name` (`module_name`),
					KEY `uid` (`uid`)
					) DEFAULT CHARSET=utf8;";
			pdo_run($sql);
		}
	}

	/**
	 *  回滚更新
	 */
	public function down() {


	}
}
