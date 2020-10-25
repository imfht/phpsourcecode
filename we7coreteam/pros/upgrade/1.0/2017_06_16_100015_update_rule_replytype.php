<?php
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * User: fanyk
 * Date: 2017/8/21
 * Time: 17:17.
 */
namespace We7\V10;

defined('IN_IA') or exit('Access Denied');
class UpdateRuleReplytype {
	public function up() {
		if (!pdo_fieldexists('rule', 'reply_type')) {
			pdo_query('ALTER TABLE '.tablename('rule').' DROP `reply_type`;');
		}
	}
}
