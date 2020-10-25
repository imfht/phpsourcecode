<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/**
 * This file will be removed in 1.6
 */

if (isset(Context::getContext()->controller))
	$controller = Context::getContext()->controller;
else
{
	$controller = new FrontController();
	$controller->init();
}
Tools::displayFileAsDeprecated();
$controller->displayFooter();
