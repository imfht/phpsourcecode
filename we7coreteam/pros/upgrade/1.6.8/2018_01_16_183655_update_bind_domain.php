<?php

namespace We7\V168;

defined('IN_IA') or exit('Access Denied');
/**
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 * Time: 1516099015
 * @version 1.6.8
 */

class UpdateBindDomain {

	/**
	 *  执行更新
	 */
	public function up() {
		if (pdo_fieldexists('uni_settings', 'bind_domain')) {
			$uni_settings = pdo_getall('uni_settings', array('bind_domain !=' => ''), array('uniacid', 'bind_domain'));
			if (!empty($uni_settings)) {
				foreach ($uni_settings as $setting) {
					$bind_domain = iunserializer($setting['bind_domain']);
					$domain = $bind_domain['domain'];
					if (empty($domain) || (!empty($domain) && starts_with($domain, 'http'))) {
						continue;
					}
					$data = array('domain' => 'http://' . $domain);
					pdo_update('uni_settings', array('bind_domain' => iserializer($data)), array('uniacid' => $setting['uniacid']));
				}
			}
		}
	}

	/**
	 *  回滚更新
	 */
	public function down() {


	}
}
