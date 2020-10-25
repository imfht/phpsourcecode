<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class PdfOrderReturnControllerCore extends FrontController
{
	protected $display_header = false;
	protected $display_footer = false;

	public function postProcess()
	{
		if (!$this->context->customer->isLogged())
			Tools::redirect('index.php?controller=authentication&back=order-follow');

		if (Tools::getValue('id_order_return') && Validate::isUnsignedId(Tools::getValue('id_order_return')))
			$this->orderReturn = new OrderReturn(Tools::getValue('id_order_return'));

		if (!isset($this->orderReturn) || !Validate::isLoadedObject($this->orderReturn))
			die(Tools::displayError('Order return not found'));
		else if ($this->orderReturn->id_customer != $this->context->customer->id)
			die(Tools::displayError('Order return not found'));
		else if ($this->orderReturn->state < 2)
			die(Tools::displayError('Order return not confirmed'));

	}

	public function display()
	{
        $pdf = new PDF($this->orderReturn, PDF::TEMPLATE_ORDER_RETURN, $this->context->smarty);
        $pdf->render();
	}
}

