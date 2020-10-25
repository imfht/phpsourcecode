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
 * @since 1.5
 */
class HTMLTemplateOrderReturnCore extends HTMLTemplate
{
	public $order_return;
	public $order;

	public function __construct(OrderReturn $order_return, $smarty)
	{
		$this->order_return = $order_return;
		$this->smarty = $smarty;
		$this->order = new Order($order_return->id_order);

		// header informations
		$this->date = Tools::displayDate($this->order->invoice_date, (int)$this->order->id_lang);
		$this->title = HTMLTemplateOrderReturn::l('Order Return ').sprintf('%06d', $this->order_return->id);

		// footer informations
		$this->shop = new Shop((int)$this->order->id_shop);
	}

	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent()
	{
		$delivery_address = new Address((int)$this->order->id_address_delivery);
		$formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
		$formatted_invoice_address = '';

		if ($this->order->id_address_delivery != $this->order->id_address_invoice)
		{
			$invoice_address = new Address((int)$this->order->id_address_invoice);
			$formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
		}

		$this->smarty->assign(array(
			'order_return' => $this->order_return,
			'return_nb_days' => (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS'),
			'products' => OrderReturn::getOrdersReturnProducts((int)$this->order_return->id, $this->order),
			'delivery_address' => $formatted_delivery_address,
			'invoice_address' => $formatted_invoice_address,
			'shop_address' => AddressFormat::generateAddress($this->shop->getAddress(), array(), '<br />', ' ')
		));
		return $this->smarty->fetch($this->getTemplate('order-return'));
	}

	/**
	 * Returns the template filename
	 * @return string filename
	 */
	public function getFilename()
	{
		return sprintf('%06d', $this->order_return->id).'.pdf';
	}

	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
	public function getBulkFilename()
	{
		return 'invoices.pdf';
	}
}

