<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class HistoryControllerCore extends FrontController
{
	public $auth = true;
	public $php_self = 'history';
	public $authRedirection = 'history';
	public $ssl = true;

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_THEME_CSS_DIR_.'history.css');
		$this->addCSS(_THEME_CSS_DIR_.'addresses.css');
		$this->addJqueryPlugin('scrollTo');
		$this->addJS(array(
					_THEME_JS_DIR_.'history.js',
					_THEME_JS_DIR_.'tools.js')
					);
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		if ($orders = Order::getCustomerOrders($this->context->customer->id))
			foreach ($orders as &$order)
			{
				$myOrder = new Order((int)$order['id_order']);
				if (Validate::isLoadedObject($myOrder))
					$order['virtual'] = $myOrder->isVirtual(false);
			}
		$this->context->smarty->assign(array(
			'orders' => $orders,
			'invoiceAllowed' => (int)(Configuration::get('PS_INVOICE')),
			'slowValidation' => Tools::isSubmit('slowvalidation')
		));

		$this->setTemplate(_PS_THEME_DIR_.'history.tpl');
	}
}

