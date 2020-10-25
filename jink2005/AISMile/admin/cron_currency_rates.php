<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

include(dirname(__FILE__).'/../config/config.inc.php');

if (isset($_GET['secure_key']))
{
	$secureKey = md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME'));
	if (!empty($secureKey) AND $secureKey === $_GET['secure_key'])
                Currency::refreshCurrencies();
}