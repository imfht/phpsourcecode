<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class OrderFollowControllerCore extends FrontController
{
	public $auth = true;
	public $php_self = 'order-follow';
	public $authRedirection = 'order-follow';
	public $ssl = true;

	/**
	 * Start forms process
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		if (Tools::isSubmit('submitReturnMerchandise'))
		{
			$customizationQtyInput = Tools::getValue('customization_qty_input');
			if (!$id_order = (int)(Tools::getValue('id_order')))
				Tools::redirect('index.php?controller=history');
			if (!$order_qte_input = Tools::getValue('order_qte_input'))
				Tools::redirect('index.php?controller=order-follow&errorDetail1');
			if (!$customizationQtyInput && $customizationIds = Tools::getValue('customization_ids'))
				Tools::redirect('index.php?controller=order-follow&errorDetail1');
			if (!$customizationIds && !$ids_order_detail = Tools::getValue('ids_order_detail'))
				Tools::redirect('index.php?controller=order-follow&errorDetail2');

			$order = new Order((int)($id_order));
			if (!$order->isReturnable()) Tools::redirect('index.php?controller=order-follow&errorNotReturnable');
			if ($order->id_customer != $this->context->customer->id)
				die(Tools::displayError());
			$orderReturn = new OrderReturn();
			$orderReturn->id_customer = (int)$this->context->customer->id;
			$orderReturn->id_order = $id_order;
			$orderReturn->question = strval(Tools::getValue('returnText'));
			if (empty($orderReturn->question))
				Tools::redirect('index.php?controller=order-follow&errorMsg&'.
					http_build_query(array(
						'ids_order_detail' => $ids_order_detail,
						'order_qte_input' => $order_qte_input,
						'id_order' => Tools::getValue('id_order'),
					)));

			if (!$orderReturn->checkEnoughProduct($ids_order_detail, $order_qte_input, $customizationIds, $customizationQtyInput))
				Tools::redirect('index.php?controller=order-follow&errorQuantity');

			$orderReturn->state = 1;
			$orderReturn->add();
			$orderReturn->addReturnDetail($ids_order_detail, $order_qte_input, $customizationIds, $customizationQtyInput);
			Hook::exec('actionOrderReturn', array('orderReturn' => $orderReturn));
			Tools::redirect('index.php?controller=order-follow');
		}
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$ordersReturn = OrderReturn::getOrdersReturn($this->context->customer->id);
		if (Tools::isSubmit('errorQuantity'))
			$this->context->smarty->assign('errorQuantity', true);
		elseif (Tools::isSubmit('errorMsg'))
			$this->context->smarty->assign(
				array(
					'errorMsg' => true,
					'ids_order_detail' => Tools::getValue('ids_order_detail', array()),
					'order_qte_input' => Tools::getValue('order_qte_input', array()),
					'id_order' => Tools::getValue('id_order'),
				)
			);
		elseif (Tools::isSubmit('errorDetail1'))
			$this->context->smarty->assign('errorDetail1', true);
		elseif (Tools::isSubmit('errorDetail2'))
			$this->context->smarty->assign('errorDetail2', true);
		elseif (Tools::isSubmit('errorNotReturnable'))
			$this->context->smarty->assign('errorNotReturnable', true);

		$this->context->smarty->assign('ordersReturn', $ordersReturn);

		$this->setTemplate(_PS_THEME_DIR_.'order-follow.tpl');
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_THEME_CSS_DIR_.'history.css');
		$this->addCSS(_THEME_CSS_DIR_.'addresses.css');
		$this->addJqueryPlugin('scrollTo');
		$this->addJS(_THEME_JS_DIR_.'history.js');
	}
}

