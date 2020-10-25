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
class HTMLTemplateDeliverySlipCore extends HTMLTemplate
{
	public $order;

	public function __construct(OrderInvoice $order_invoice, $smarty)
	{
		$this->order_invoice = $order_invoice;
		$this->order = new Order($this->order_invoice->id_order);
		$this->smarty = $smarty;

		// header informations
		$this->date = Tools::displayDate($this->order->invoice_date, (int)$this->order->id_lang);
		$this->title = HTMLTemplateDeliverySlip::l('Delivery').' #'.Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id).sprintf('%06d', $this->order_invoice->delivery_number);

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
			'order' => $this->order,
			'order_details' => $this->order_invoice->getProducts(),
			'delivery_address' => $formatted_delivery_address,
			'invoice_address' => $formatted_invoice_address,
			'order_invoice' => $this->order_invoice
		));

		return $this->smarty->fetch($this->getTemplate('delivery-slip'));
	}

	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
	public function getBulkFilename()
	{
		return 'deliveries.pdf';
	}

	/**
	 * Returns the template filename
	 * @return string filename
	 */
	public function getFilename()
	{
		return Configuration::get('PS_DELIVERY_PREFIX').sprintf('%06d', $this->order->invoice_number).'.pdf';
	}
}

