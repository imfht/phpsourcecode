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
include(dirname(__FILE__).'/../../init.php');
if ( isset($_POST['ajax_blockcart_display']) || isset($_GET['ajax_blockcart_display']))
{
	if (Tools::getValue('ajax_blockcart_display') == 'collapse')
	{
		Context::getContext()->cookie->ajax_blockcart_display = 'collapsed';
		die ('collapse status of the blockcart module updated in the cookie');
	}
	if (Tools::getValue('ajax_blockcart_display') == 'expand')
	{
		Context::getContext()->cookie->ajax_blockcart_display = 'expanded';
		die ('expand status of the blockcart module updated in the cookie');
	}
	die ('ERROR : bad status setted. Only collapse or expand status of the blockcart module are available.');
}
else die('ERROR : No status setted.');

