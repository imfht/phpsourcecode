<?php
namespace We7\V157;
defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1505212727
 * @version 1.5.7
 */
class UpdateAccountWxapp {

	public $description = '小程序访问域名设置';
	/**
	 *  执行更新
	 */
	public function up() {
		$exists = pdo_fieldexists('account_wxapp','appdomain');
		$table = tablename('account_wxapp');
		if(!$exists) {
			$sql = <<<EOT
				ALTER TABLE $table ADD appdomain VARCHAR(255) DEFAULT '' COMMENT '小程序访问域名'
EOT;
			pdo_query($sql);
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		