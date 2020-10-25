<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class MyAccountControllerCore extends FrontController
{
	public $auth = true;
	public $php_self = 'my-account';
	public $authRedirection = 'my-account';
	public $ssl = true;

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_THEME_CSS_DIR_.'my-account.css');
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$has_address = $this->context->customer->getAddresses($this->context->language->id);
		$this->context->smarty->assign(array(
			'has_customer_an_address' => empty($has_address),
			'voucherAllowed' => (int)CartRule::isFeatureActive(),
			'returnAllowed' => (int)Configuration::get('PS_ORDER_RETURN')
		));
		$this->context->smarty->assign('HOOK_CUSTOMER_ACCOUNT', Hook::exec('displayCustomerAccount'));

		$this->setTemplate(_PS_THEME_DIR_.'my-account.tpl');
	}
}

