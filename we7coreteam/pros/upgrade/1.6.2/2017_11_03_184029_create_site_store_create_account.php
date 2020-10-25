<?php

namespace We7\V162;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1509705629
 * @version 1.6.2
 */

class CreateSiteStoreCreateAccount {

	/**
	 *  执行更新
	 */
	public function up() {
		$sql = "CREATE TABLE IF NOT EXISTS " . tablename('site_store_create_account') . " (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `uniacid` int(10) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1.公众号 4.小程序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=13 ;";
		pdo_run($sql);
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		