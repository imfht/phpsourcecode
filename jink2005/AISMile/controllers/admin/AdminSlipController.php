<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class AdminSlipControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'order_slip';
		$this->className = 'OrderSlip';
		$this->addRowAction('delete');

		$this->fields_list = array(
			'id_order_slip' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
 			),
			'id_order' => array(
				'title' => $this->l('ID Order'),
				'align' => 'left'
 			),
			'date_add' => array(
				'title' => $this->l('Date issued'),
				'width' => 150,
				'type' => 'date',
				'align' => 'right'
 			)
 		);
	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->optionTitle = $this->l('Slip');

		parent::__construct();
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Print a PDF'),
				'image' => '../img/admin/pdf.gif'
			),
			'input' => array(
				array(
					'type' => 'date',
					'label' => $this->l('From:'),
					'name' => 'date_from',
					'size' => 20,
					'maxlength' => 10,
					'required' => true,
					'desc' => $this->l('Format: 2011-12-31 (inclusive)')
				),
				array(
					'type' => 'date',
					'label' => $this->l('To:'),
					'name' => 'date_to',
					'size' => 20,
					'maxlength' => 10,
					'required' => true,
					'desc' => $this->l('Format: 2012-12-31 (inclusive)')
				)
			),
			'submit' => array(
				'title' => $this->l('Generate PDF file'),
				'class' => 'button',
				'id' => 'submitPrint'
			)
		);

		$this->fields_value = array(
			'date_from' => date('Y-m-d'),
			'date_to' => date('Y-m-d')
		);

		$this->show_toolbar = false;
		return parent::renderForm();
	}

	public function postProcess()
	{
		if (Tools::getValue('submitAddorder_slip'))
		{
			if (!Validate::isDate(Tools::getValue('date_from')))
				$this->errors[] = $this->l('Invalid "From" date');
			if (!Validate::isDate(Tools::getValue('date_to')))
				$this->errors[] = $this->l('Invalid "To" date');
			if (!count($this->errors))
			{
				$order_slips = OrderSlip::getSlipsIdByDate(Tools::getValue('date_from'), Tools::getValue('date_to'));
				if (count($order_slips))
					Tools::redirectAdmin($this->context->link->getAdminLink('AdminPdf').'&submitAction=generateOrderSlipsPDF&date_from='.urlencode(Tools::getValue('date_from')).'&date_to='.urlencode(Tools::getValue('date_to')));
				$this->errors[] = $this->l('No order slips found for this period');
			}
		}
		else
			return parent::postProcess();
	}

	public function initContent()
	{
		$this->initToolbar();
		$this->content .= $this->renderList();
		$this->content .= $this->renderForm();

		$this->context->smarty->assign(array(
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,
		));
	}

	public function initToolbar()
	{
		$this->toolbar_btn['save-date'] = array(
			'href' => '#',
			'desc' => $this->l('Generate PDF file')
		);
	}
}

