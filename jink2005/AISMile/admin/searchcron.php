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

if (substr(_COOKIE_KEY_, 34, 8) != Tools::getValue('token'))
	die;

ini_set('max_execution_time', 7200);
Search::indexation(Tools::getValue('full'));
if (Tools::getValue('redirect'))
Tools::redirectAdmin($_SERVER['HTTP_REFERER'].'&conf=4');

