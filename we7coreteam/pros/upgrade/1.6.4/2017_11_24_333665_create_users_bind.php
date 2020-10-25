<?php

namespace We7\V164;

defined('IN_IA') or exit('Access Denied');

class CreateUsersBind {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_tableexists('users_bind')) {
			$sql = "CREATE TABLE `ims_users_bind` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `uid` int(11) NOT NULL COMMENT '用户uid',
				  `bind_sign` varchar(50) NOT NULL DEFAULT '' COMMENT '绑定标识openid或者手机号',
				  `third_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '绑定类型 1:qq  2:weixin 3手机号',
				  `third_nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '第三方昵称',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `bind_sign` (`bind_sign`),
				  KEY `uid` (`uid`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			pdo_run($sql);
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
	}
}
		