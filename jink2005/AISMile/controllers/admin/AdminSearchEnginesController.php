<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class AdminSearchEnginesControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'search_engine';
	 	$this->className = 'SearchEngine';
	 	$this->lang = false;

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->fields_list = array(
			'id_search_engine' => array('title' => $this->l('ID'), 'width' => 25),
			'server' => array('title' => $this->l('Server')),
			'getvar' => array('title' => $this->l('GET variable'), 'width' => 100)
		);

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Referrer')
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Server:'),
					'name' => 'server',
					'size' => 20,
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('$_GET variable:'),
					'name' => 'getvar',
					'size' => 40,
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


