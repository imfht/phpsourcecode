<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class AdminOutstandingControllerCore  extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'order_invoice';
		$this->className = 'OrderInvoice';
		$this->addRowAction('view');

		$this->context = Context::getContext();

		$this->_select = '`id_order_invoice` AS `id_invoice`,
		`id_order_invoice` AS `outstanding`,
		CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
		c.`outstanding_allow_amount`,
		r.`color`,
		rl.`name` AS `risk`';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = a.`id_order`)
		LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)
		LEFT JOIN `'._DB_PREFIX_.'risk` r ON (r.`id_risk` = c.`id_risk`)
		LEFT JOIN `'._DB_PREFIX_.'risk_lang` rl ON (r.`id_risk` = rl.`id_risk` AND rl.`id_lang` = '.(int)$this->context->language->id.')';

		$risks = array();
		foreach (Risk::getRisks() as $risk)
			$risks[$risk->id] = $risk->name;

		$this->fields_list = array(
			'number' => array(
				'title' => $this->l('Invoice Number'),
				'align' => 'center',
				'width' => 20
 			),
			'date_add' => array(
				'title' => $this->l('Date'),
				'width' => 150,
				'type' => 'date',
				'align' => 'right',
				'filter_key' => 'a!date_add'
 			),
			'customer' => array(
				'title' => $this->l('Customer'),
				'filter_key' => 'customer',
				'tmpTableFilter' => true
 			),
			'company' => array(
				'title' => $this->l('Company'),
				'align' => 'center',
				'width' => 20
 			),
			'risk' => array(
				'title' => $this->l('Risk'),
				'align' => 'center',
				'width' => 100,
				'orderby' => false,
				'type' => 'select',
				'color' => 'color',
				'list' => $risks,
				'filter_key' => 'r!id_risk',
				'filter_type' => 'int'
 			),
			'outstanding_allow_amount' => array(
				'title' => $this->l('Outstanding Allow'),
				'align' => 'center',
				'width' => 50,
				'prefix' => '<b>',
				'suffix' => '</b>',
				'type' => 'price'
 			),
			'outstanding' => array(
				'title' => $this->l('Current Outstanding'),
				'align' => 'center',
				'width' => 50,
				'callback' => 'printOutstandingCalculation',
				'orderby' => false,
				'search' => false
 			),
			'id_invoice' => array(
				'title' => $this->l('PDF'),
				'width' => 35,
				'align' => 'center',
				'callback' => 'printPDFIcons',
				'orderby' => false,
				'search' => false
			)
 		);

		parent::__construct();
	}

	/**
	 * Toolbar initialisation
	 * @return bool Force true (Hide New button)
	 */
	public function initToolbar()
	{
		return true;
	}

	/**
	 * Column callback for print PDF incon
	 * @param $id_invoice integer Invoice ID
	 * @param $tr array Row data
	 * @return string HTML content
	 */
	public function printPDFIcons($id_invoice, $tr)
	{
		$this->context->smarty->assign(array(
			'id_invoice' => $id_invoice
		));

		return $this->createTemplate('_print_pdf_icon.tpl')->fetch();
	}

	public function printOutstandingCalculation($id_invoice, $tr)
	{
		$order_invoice = new OrderInvoice($id_invoice);
		if (!Validate::isLoadedObject($order_invoice))
			throw new MileBizException('object OrderInvoice can\'t be loaded');
		$order = new Order($order_invoice->id_order);
		if (!Validate::isLoadedObject($order))
			throw new MileBizException('object Order can\'t be loaded');
		$customer = new Customer((int)$order->id_customer);
		if (!Validate::isLoadedObject($order_invoice))
			throw new MileBizException('object Customer can\'t be loaded');

		return '<b>'.$customer->getOutstanding().'</b>';
	}

	/**
	 * View render
	 * @throws MileBizException Invalid objects
	 */
	public function renderView()
	{
		$order_invoice = new OrderInvoice((int)Tools::getValue('id_order_invoice'));
		if (!Validate::isLoadedObject($order_invoice))
			throw new MileBizException('object OrderInvoice can\'t be loaded');
		$order = new Order($order_invoice->id_order);
		if (!Validate::isLoadedObject($order))
			throw new MileBizException('object Order can\'t be loaded');

		$link = $this->context->link->getAdminLink('AdminOrders');
		$link .= '&vieworder&id_order='.$order->id;
		$this->redirect_after = $link;
		$this->redirect();
	}
}

