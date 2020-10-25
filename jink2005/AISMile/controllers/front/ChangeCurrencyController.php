<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class ChangeCurrencyControllerCore extends FrontController
{
	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		$currency = new Currency((int)Tools::getValue('id_currency'));
		if (Validate::isLoadedObject($currency) && !$currency->deleted)
		{
			$this->context->cookie->id_currency = (int)$currency->id;
			die('1');
		}
		die('0');
	}
}