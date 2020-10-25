<?php

namespace We7\V164;

defined('IN_IA') or exit('Access Denied');

class CreateMessageNotice {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_tableexists('message_notice_log')) {
			$sql = "CREATE TABLE `ims_message_notice_log` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `message` varchar(255) NOT NULL DEFAULT '' COMMENT '消息内容',
			  `is_read` tinyint(3) NOT NULL DEFAULT '1' COMMENT '1未读，2已读',
			  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户的uid',
			  `sign` varchar(22) NOT NULL DEFAULT '' COMMENT '订单id,公众号uniacid,工单的id,注册uid',
			  `type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '1订单，2公众号到期，3工单，4注册，5小程序到期',
			  `status` tinyint(3) DEFAULT '0' COMMENT '是否需要审核 1、注册审核；2、正常',
			  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '提交时间',
			  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '到期时间',
			  PRIMARY KEY (`id`)
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
		