<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class PageNotFoundControllerCore extends FrontController
{
	public $php_self = '404';
	public $page_name = 'pagenotfound';

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		header('HTTP/1.1 404 Not Found');
		header('Status: 404 Not Found');
		parent::initContent();

		$this->setTemplate(_PS_THEME_DIR_.'404.tpl');
	}
}

