<?php

namespace We7\V163;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1510208155
 * @version 1.6.2
 */

class UpdateArticleNotice {

	/**
	 *  执行更新
	 */
	public function up() {
		if (!pdo_fieldexists('article_notice', 'style')) {
			pdo_query('ALTER TABLE ' . tablename('article_notice') . " ADD `style` varchar(200) NOT NULL DEFAULT '';");
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		