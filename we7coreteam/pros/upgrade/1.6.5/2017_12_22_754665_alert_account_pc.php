<?php

namespace We7\V165;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1510292752
 * @version 1.6.5
 */

class AlertAccountPc {

	/**
	 *  执行更新
	 */
	public function up() {
		if(!pdo_indexexists('account_webapp', 'uniacid')){
			pdo_query("ALTER TABLE " .tablename('account_webapp') ." ADD INDEX uniacid (`uniacid`)");
		}
		if(!pdo_indexexists('account_webapp', 'acid')){
			pdo_query("ALTER TABLE " .tablename('account_webapp') . " ADD PRIMARY KEY (`acid`)");
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		