<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class AdminAddonsCatalogControllerCore extends AdminController
{
	public function initContent()
	{
		$this->context->smarty->assign('parentDomain', Tools::getHttpHost(true).substr($_SERVER['REQUEST_URI'], 0, -1 * strlen(basename($_SERVER['REQUEST_URI']))));
		parent::initContent();
	}
}


