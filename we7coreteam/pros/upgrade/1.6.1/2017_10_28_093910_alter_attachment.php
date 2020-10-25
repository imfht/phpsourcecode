<?php

namespace We7\V161;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1509154750
 * @version 1.6.1
 */

class AlterAttachment {

	/**
	 *  执行更新
	 */
	public function up() {
		if(!pdo_fieldexists('core_attachment', 'module_upload_dir')) {
			pdo_query('ALTER TABLE ' . tablename('core_attachment') . " ADD `module_upload_dir` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '第三方应用传递的自定义上传目录hash值';");
		}

		if(!pdo_fieldexists('wechat_attachment', 'module_upload_dir')) {
			pdo_query('ALTER TABLE ' . tablename('wechat_attachment') . " ADD `module_upload_dir` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '第三方应用传递的自定义上传目录hash值';");
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		