<?php

namespace We7\V170;

defined('IN_IA') or exit('Access Denied');

class CreatePhoneappVsersions {

	/**
	 *  执行更新
	 */
	public function up() {
		if(!pdo_tableexists('phoneapp_versions')){
			$table_name = tablename('phoneapp_versions');
			$sql = <<<EOT
				CREATE TABLE $table_name (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(10) unsigned NOT NULL COMMENT 'uniacid',
  `version` varchar(20) DEFAULT NULL COMMENT '版本号',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '版本描述',
  `modules` text,
  `createtime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `version` (`version`),
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
		