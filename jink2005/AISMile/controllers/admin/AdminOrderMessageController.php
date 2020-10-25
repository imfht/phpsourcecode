<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class AdminOrderMessageControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'order_message';
		$this->className = 'OrderMessage';
	 	$this->lang = true;

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->fields_list = array(
			'id_order_message' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 140
			),
			'message' => array(
				'title' => $this->l('Message'),
				'width' => 600,
				'maxlength' => 300
			)
		);

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Order messages'),
				'image' => '../img/admin/email.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'lang' => true,
					'label' => $this->l('Name:'),
					'name' => 'name',
					'size' => 53,
					'required' => true
				),
				array(
					'type' => 'textarea',
					'lang' => true,
					'label' => $this->l('Message:'),
					'name' => 'message',
					'cols' => 50,
					'rows' => 15,
					'required' => true
				)
			),
			'submit' => array(
				'title' => $this->l('Save   '),
				'class' => 'button'
			)
		);

		parent::__construct();
	}

}


