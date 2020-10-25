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
include(dirname(__FILE__).'/trackingfront.php');

$tf = new TrackingFront();
if (!$tf->active)
	Tools::redirect('index.php?controller=404');
$tf->postProcess();
echo $tf->isLogged() ? $tf->displayAccount() : $tf->displayLogin();

