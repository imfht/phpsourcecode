<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class OrderConfirmationControllerCore extends FrontController
{
	public $php_self = 'order-confirmation';

	public $id_cart;
	public $id_module;
	public $id_order;
	public $reference;
	public $secure_key;

	/**
	 * Initialize order confirmation controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		parent::init();

		$this->id_cart = (int)(Tools::getValue('id_cart', 0));
		$is_guest = false;

		/* check if the cart has been made by a Guest customer, for redirect link */
		if (Cart::isGuestCartByCartId($this->id_cart))
		{
			$is_guest = true;
			$redirectLink = 'index.php?controller=guest-tracking';
		}
		else
			$redirectLink = 'index.php?controller=history';

		$this->id_module = (int)(Tools::getValue('id_module', 0));
		$this->id_order = Order::getOrderByCartId((int)($this->id_cart));
		$this->secure_key = Tools::getValue('key', false);
		$order = new Order((int)($this->id_order));
		if ($is_guest)
		{
			$customer = new Customer((int)$order->id_customer);
			$redirectLink .= '&id_order='.$order->reference.'&email='.urlencode($customer->email);
		}
		if (!$this->id_order || !$this->id_module || !$this->secure_key || empty($this->secure_key))
			Tools::redirect($redirectLink.(Tools::isSubmit('slowvalidation') ? '&slowvalidation' : ''));
		$this->reference = $order->reference;
		if (!Validate::isLoadedObject($order) || $order->id_customer != $this->context->customer->id || $this->secure_key != $order->secure_key)
			Tools::redirect($redirectLink);
		$module = Module::getInstanceById((int)($this->id_module));
		if ($order->payment != $module->displayName)
			Tools::redirect($redirectLink);
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$this->context->smarty->assign(array(
			'is_guest' => $this->context->customer->is_guest,
			'HOOK_ORDER_CONFIRMATION' => $this->displayOrderConfirmation(),
			'HOOK_PAYMENT_RETURN' => $this->displayPaymentReturn()
		));

		if ($this->context->customer->is_guest)
		{
			$this->context->smarty->assign(array(
				'id_order' => $this->id_order,
				'reference_order' => $this->reference,
				'id_order_formatted' => sprintf('#%06d', $this->id_order),
				'email' => $this->context->customer->email
			));
			/* If guest we clear the cookie for security reason */
			$this->context->customer->mylogout();
		}

		$this->setTemplate(_PS_THEME_DIR_.'order-confirmation.tpl');
	}

	/**
	 * Execute the hook displayPaymentReturn
	 */
	public function displayPaymentReturn()
	{
		if (Validate::isUnsignedId($this->id_order) && Validate::isUnsignedId($this->id_module))
		{
			$params = array();
			$order = new Order($this->id_order);
			$currency = new Currency($order->id_currency);

			if (Validate::isLoadedObject($order))
			{
				$params['total_to_pay'] = $order->getOrdersTotalPaid();
				$params['currency'] = $currency->sign;
				$params['objOrder'] = $order;
				$params['currencyObj'] = $currency;

				return Hook::exec('displayPaymentReturn', $params, $this->id_module);
			}
		}
		return false;
	}

	/**
	 * Execute the hook displayOrderConfirmation
	 */
	public function displayOrderConfirmation()
	{
		if (Validate::isUnsignedId($this->id_order))
		{
			$params = array();
			$order = new Order($this->id_order);
			$currency = new Currency($order->id_currency);

			if (Validate::isLoadedObject($order))
			{
				$params['total_to_pay'] = $order->getOrdersTotalPaid();
				$params['currency'] = $currency->sign;
				$params['objOrder'] = $order;
				$params['currencyObj'] = $currency;

				return Hook::exec('displayOrderConfirmation', $params);
			}
		}
		return false;
	}
}

