<?php

namespace We7\V167;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1515651148
 * @version 1.6.7
 */

class UpdateFansInfo {

	/**
	 *  执行更新
	 */
	public function up() {
		pdo_run("UPDATE ims_mc_mapping_fans set tag=REPLACE (tag, 'MTMyMTMy', 'MTMy')");
	}

	/**
	 *  回滚更新
	 */
	public function down() {


	}
}
