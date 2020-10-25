<?php

namespace We7\V170;

defined('IN_IA') or exit('Access Denied');

class CreateAccountPhone {

	/**
	 *  执行更新
	 */
	public function up() {
		if(!pdo_tableexists('account_phoneapp')){
			$table_name = tablename('account_phoneapp');
			$sql = <<<EOT
				CREATE TABLE $table_name (
  `acid` int(11) NOT NULL DEFAULT '0',
  `uniacid` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT '',
  PRIMARY KEY (`acid`),
  KEY `uniacid` (`uniacid`)
) DEFAULT CHARSET=utf8;
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
		