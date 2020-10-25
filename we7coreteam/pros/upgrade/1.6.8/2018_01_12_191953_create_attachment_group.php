<?php

namespace We7\V168;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1515755993
 * @version 1.6.8
 */

class CreateAttachmentGroup {

	/**
	 *  执行更新
	 */
	public function up() {
		$table_name = tablename('attachment_group');
		if (!pdo_tableexists('attachment_group')) {
			$sql = <<<EOT
				CREATE TABLE $table_name (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL DEFAULT '' COMMENT '组名',
  `uniacid` int(11) DEFAULT '0' COMMENT 'uniacid',
  `uid` int(11) DEFAULT '0' COMMENT '用户ID',
  `type` tinyint(1) DEFAULT '0' COMMENT '0 本地 1 微信',
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
EOT;
			pdo_query($sql);
		}


		if (!pdo_fieldexists('core_attachment', 'group_id')) {
			$table_name = tablename('core_attachment');
			$core_sql = <<<EOT
		ALTER TABLE $table_name ADD `group_id` int(11) DEFAULT 0
EOT;
			pdo_query($core_sql);
		}

		if (!pdo_fieldexists('wechat_attachment', 'group_id')) {
			$table_name = tablename('wechat_attachment');
			$wechat_sql = <<<EOT
		ALTER TABLE $table_name ADD `group_id` int(11) DEFAULT 0
EOT;
			pdo_query($wechat_sql);
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		