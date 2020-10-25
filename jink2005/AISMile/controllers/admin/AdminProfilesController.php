<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class AdminProfilesControllerCore extends AdminController
{
	public function __construct()
	{
		$this->context = Context::getContext();
		$this->table = 'profile';
		$this->className = 'Profile';
		$this->multishop_context = Shop::CONTEXT_ALL;
		$this->lang = true;
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->addRowActionSkipList('delete', array(1));

		$this->bulk_actions = array(
			'delete' => array('text' => $this->l('Delete selected'), 
			'confirm' => $this->l('Delete selected items?'))
			);

		$this->fields_list = array(
			'id_profile' => array(
						'title' => $this->l('ID'), 
						'align' => 'center', 
						'width' => 25
						),
			'name' => array('title' => $this->l('Name'))
			);
			
		$this->identifier = 'id_profile';

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Profile'),
				'image' => '../img/admin/profiles.png'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'size' => 33,
					'required' => true,
					'lang' => true,
				)
			),
			'submit' => array(
				'title' => $this->l('Save   '),
				'class' => 'button'
			)
		);

		$list_profile = array();
		foreach (Profile::getProfiles($this->context->language->id) as $profil)
			$list_profile[] = array('value' => $profil['id_profile'], 'name' => $profil['name']);

		parent::__construct();
	}

	public function postProcess()
	{
		/* MileBiz demo mode */
		if (_PS_MODE_DEMO_)
		{
			$this->errors[] = Tools::displayError('This functionality has been disabled.');
			return;
		}
		/* MileBiz demo mode*/

		if (isset($_GET['delete'.$this->table]) && $_GET[$this->identifier] == (int)(_PS_ADMIN_PROFILE_))
			$this->errors[] = $this->l('For security reasons, you cannot delete the Administrator profile');
		else
			parent::postProcess();
	}
}


