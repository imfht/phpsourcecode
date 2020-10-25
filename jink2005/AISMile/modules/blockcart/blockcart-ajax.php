<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */
// @TODO Find the reason why the blockcart.php is includ multiple time
include_once(dirname(__FILE__).'/blockcart.php');
$context = Context::getContext();
$blockCart = new BlockCart();
echo $blockCart->hookAjaxCall(array('cookie' => $context->cookie, 'cart' => $context->cart));