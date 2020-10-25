<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class AdminTagsControllerCore extends AdminController
{
	public function __construct()
	{
		$this->table = 'tag';
		$this->className = 'Tag';

		$this->fields_list = array(
			'id_tag' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25,
			),
			'lang' => array(
				'title' => $this->l('Language'),
				'filter_key' => 'l!name'
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 200,
				'filter_key' => 'a!name'
			),
			'products' => array(
				'title' => $this->l('Products'),
				'align' => 'right',
				'havingFilter' => true
			)
		);

	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		parent::__construct();
	}

	public function renderList()
	{
		$this->addRowAction('edit');
	 	$this->addRowAction('delete');

		$this->_select = 'l.name as lang, COUNT(pt.id_product) as products';
		$this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'product_tag` pt
				ON (a.`id_tag` = pt.`id_tag`)
			LEFT JOIN `'._DB_PREFIX_.'lang` l
				ON (l.`id_lang` = a.`id_lang`)';
		$this->_group = 'GROUP BY a.name, a.id_lang';

		return parent::renderList();
	}

	public function postProcess()
	{
		if ($this->tabAccess['edit'] === '1' && Tools::getValue('submitAdd'.$this->table))
			if (($id = (int)Tools::getValue($this->identifier)) && ($obj = new $this->className($id)) && Validate::isLoadedObject($obj))
				$obj->setProducts($_POST['products']);
		return parent::postProcess();
	}

	public function renderForm()
	{
		if (!($obj = $this->loadObject(true)))
			return;

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Tag')
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'required' => true
				),
				array(
					'type' => 'select',
					'label' => $this->l('Language:'),
					'name' => 'id_lang',
					'required' => true,
					'options' => array(
						'query' => Language::getLanguages(false),
						'id' => 'id_lang',
						'name' => 'name'
					)
				),
			),
			'selects' => array(
				'products' => $obj->getProducts(true),
				'products_unselected' => $obj->getProducts(false)
			),
			'submit' => array(
				'title' => $this->l('Save   '),
				'class' => 'button'
			)
		);

		return parent::renderForm();
	}
}


