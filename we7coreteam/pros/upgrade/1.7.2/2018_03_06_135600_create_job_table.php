<?php

namespace We7\V172;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1520315760
 * @version 1.7.2
 */

class CreateJobTable {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_tableexists('job')) {
			$table = tablename('job');
			$sql = "CREATE TABLE IF NOT EXISTS $table (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `type` int(11) NOT NULL DEFAULT 0 COMMENT '任务类型 10 删除公众号数据 20 同步粉丝 ',
			  `uniacid` int(11) NOT NULL DEFAULT 0 COMMENT 'uniacid',
			  `payload` varchar(255) NOT NULL DEFAULT '' COMMENT '任务附加参数',
			  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '1 完成，0 新建',
			  `doing` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0 未开始执行，1 正在执行中 不允许任务并列执行',
			  `title` varchar(22) NOT NULL DEFAULT '' COMMENT '任务中文描述',
			  `handled` int(11) NOT NULL DEFAULT '0' COMMENT '已处理数量',
			  `total` int(11) NOT NULL DEFAULT '0' COMMENT '处理总数量',
			  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '开始时间',
			  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
			  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '结束时间',
			  PRIMARY KEY (`id`)
			)  DEFAULT CHARSET=utf8;";
			pdo_run($sql);
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		