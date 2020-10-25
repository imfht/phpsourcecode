<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/followup.php');

if (isset($_GET['secure_key']))
{
	$secureKey = Configuration::get('PS_FOLLOWUP_SECURE_KEY');
	if (!empty($secureKey) AND $secureKey === $_GET['secure_key'])
	{
		$followup = new Followup();
		$followup->cronTask();
	}
}

