<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class AdminContactsControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'contact';
	 	$this->className = 'Contact';
	 	$this->lang = true;
		$this->addRowAction('edit');
		$this->addRowAction('delete');
	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->fields_list = array(
			'id_contact' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'name' => array('title' => $this->l('Title'), 'width' => 130),
			'email' => array('title' => $this->l('E-mail address'), 'width' => 130),
			'description' => array('title' => $this->l('Description'), 'width' => 150),
		);

		parent::__construct();
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Contacts'),
				'image' => '../img/admin/contact.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Title:'),
					'name' => 'name',
					'size' => 33,
					'required' => true,
					'lang' => true,
					'desc' => $this->l('Contact name (e.g. Technical Support)'),
				),
				array(
					'type' => 'text',
					'label' => $this->l('E-mail address'),
					'name' => 'email',
					'size' => 33,
					'required' => false,
					'desc' => $this->l('E-mails will be sent to this address'),
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Save messages?'),
					'name' => 'customer_service',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'desc' => $this->l('If enabled, all messages will be saved in the "Customer Service" page under the "Customer" menu'),
					'values' => array(
						array(
							'id' => 'customer_service_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'customer_service_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Description'),
					'name' => 'description',
					'required' => false,
					'lang' => true,
					'cols' => 36,
					'rows' => 5,
					'desc' => $this->l('Additional information about this contact'),
				),
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);
		
		if (Shop::isFeatureActive())
		{
			$this->fields_form['input'][] = array(
				'type' => 'shop',
				'label' => $this->l('Shop association:'),
				'name' => 'checkBoxShopAsso',
			);
		}

		return parent::renderForm();
	}

}


