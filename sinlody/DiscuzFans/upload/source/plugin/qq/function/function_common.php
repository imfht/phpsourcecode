<?php

/**
 * 维清 [ Discuz!应用专家，深圳市维清互联科技有限公司旗下Discuz!开发团队 ]
 *
 * Copyright (c) 2011-2099 http://www.wikin.cn All rights reserved.
 *
 * Author: wikin <wikin@wikin.cn>
 *
 * $Id: function_common.php 2016-4-17 13:59:28Z $
 */
if (!function_exists('currentlang')) {

	function currentlang() {
		$charset = strtoupper(CHARSET);
		if ($charset == 'GBK') {
			return 'SC_GBK';
		} elseif ($charset == 'BIG5') {
			return 'TC_BIG5';
		} elseif ($charset == 'UTF-8') {
			global $_G;
			if ($_G['config']['output']['language'] == 'zh_cn') {
				return 'SC_UTF8';
			} elseif ($_G['config']['output']['language'] == 'zh_tw') {
				return 'TC_UTF8';
			}
		} else {
			return '';
		}
	}

}
?>