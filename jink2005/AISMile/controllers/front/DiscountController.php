<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class DiscountControllerCore extends FrontController
{
	public $auth = true;
	public $php_self = 'discount';
	public $authRedirection = 'discount';
	public $ssl = true;

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$cart_rules = CartRule::getCustomerCartRules($this->context->language->id, $this->context->customer->id, true, false);
		$nb_cart_rules = count($cart_rules);

		$this->context->smarty->assign(array('nb_cart_rules' => (int)$nb_cart_rules, 'cart_rules' => $cart_rules));
		$this->setTemplate(_PS_THEME_DIR_.'discount.tpl');
	}
}

