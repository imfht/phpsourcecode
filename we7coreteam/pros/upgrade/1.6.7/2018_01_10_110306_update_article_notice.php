<?php

namespace We7\V167;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1515553386
 * @version 1.6.7
 */

class UpdateArticleNotice {

	/**
	 *  执行更新
	 */
	public function up() {
		if(!pdo_fieldexists('article_notice', 'group')) {
			pdo_query("ALTER TABLE " . tablename('article_notice') . " ADD `group` varchar(255) NOT NULL DEFAULT 0;");
		}
	}

	/**
	 *  回滚更新
	 */
	public function down() {


	}
}
