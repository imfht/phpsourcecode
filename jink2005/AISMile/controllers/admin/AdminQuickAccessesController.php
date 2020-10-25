<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class AdminQuickAccessesControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'quick_access';
		$this->className = 'QuickAccess';
	 	$this->lang = true;

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->fields_list = array(
			'id_quick_access' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 200
			),
			'link' => array(
				'title' => $this->l('Link'),
				'width' => 300
			),
			'new_window' => array(
				'title' => $this->l('New window'),
				'width' => 70,
				'align' => 'center',
				'type' => 'bool',
				'activeVisu' => 'new_window'
			)
		);

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Quick Access menu'),
				'image' => '../img/admin/quick.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'lang' => true,
					'size' => 33,
					'maxlength' => 32,
					'required' => true,
					'hint' => $this->l('Forbidden characters:').' <>;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('URL:'),
					'name' => 'link',
					'size' => 60,
					'maxlength' => 128,
					'required' => true,
					'desc' => $this->l('If it\'s an URL that comes from your Back Office, you must NOT put a security token.')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Open in new window:'),
					'name' => 'new_window',
					'required' => false,
					'class' => 't',
					'values' => array(
						array(
							'id' => 'new_window_on',
							'value' => 1,
							'label' => '<img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" />'
						),
						array(
							'id' => 'new_window_off',
							'value' => 0,
							'label' => '<img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" />'
						)
					)
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		parent::__construct();
	}
}


