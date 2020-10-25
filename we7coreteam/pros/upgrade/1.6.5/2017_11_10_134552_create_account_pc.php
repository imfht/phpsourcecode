<?php

namespace We7\V165;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1510292752
 * @version 1.6.5
 */

class CreateAccountPc {

	/**
	 *  执行更新
	 */
	public function up() {
		if(!pdo_tableexists('account_webapp')){
			$sql = <<<EOT
				CREATE TABLE `ims_account_webapp` (
  `acid` int(11) DEFAULT NULL,
  `uniacid` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT ''
) DEFAULT CHARSET=utf8;
EOT;

			pdo_query($sql);
		}
	}
	
	/**
	 *  回滚更新
	 */
	public function down() {
		

	}
}
		