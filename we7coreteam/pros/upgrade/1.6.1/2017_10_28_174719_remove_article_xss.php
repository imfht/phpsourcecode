<?php

namespace We7\V161;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1509184039
 * @version 1.6.1
 */

class RemoveArticleXss {

	/**
	 *  执行更新
	 */
	public function up() {
		$article_list = pdo_getall('site_article');
		if (is_array($article_list)) {
			foreach ($article_list as $article) {
				pdo_update('site_article', array('content' => safe_remove_xss($article['content'])), array('id' => $article['id']));
			}
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		