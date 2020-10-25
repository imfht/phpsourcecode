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
 * @deprecated 1.5.0 This file is deprecated, use moduleFrontController instead
 */

/* SSL Management */
$useSSL = true;

require('../../config/config.inc.php');
Tools::displayFileAsDeprecated();

// init front controller in order to use Tools::redirect
$controller = new FrontController();
$controller->init();

Tools::redirect(Context::getContext()->link->getModuleLink('favoriteproducts', 'account'));