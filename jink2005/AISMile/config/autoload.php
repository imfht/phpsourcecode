<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

// Include some alias functions
require_once(dirname(__FILE__).'/alias.php');
require_once(dirname(__FILE__).'/../classes/Autoload.php');

spl_autoload_register(array(Autoload::getInstance(), 'load'));
