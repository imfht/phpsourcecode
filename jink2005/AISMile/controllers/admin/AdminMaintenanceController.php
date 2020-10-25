<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class AdminMaintenanceControllerCore extends AdminController
{
	public function __construct()
	{
		$this->className = 'Configuration';
		$this->table = 'configuration';

		parent::__construct();

		$this->fields_options = array(
			'general' => array(
				'title' =>	$this->l('General'),
				'icon' =>	'tab-preferences',
				'fields' =>	array(
					'PS_SHOP_ENABLE' => array(
						'title' => $this->l('Enable Shop'),
						'desc' => $this->l('Activate or deactivate your shop. It is a good idea to deactivate your shop while you perform maintenance on it. Please note that the webservice will not be disabled'),
						'validation' => 'isBool',
						'cast' => 'intval',
						'type' => 'bool'
					),
					'PS_MAINTENANCE_IP' => array(
						'title' => $this->l('Maintenance IP'),
						'desc' => $this->l('IP addresses allowed to access the Front Office even if the shop is disabled. Use a comma to separate them (e.g. 42.24.4.2,127.0.0.1,99.98.97.96)'),
						'validation' => 'isGenericName',
						'type' => 'maintenance_ip',
						'size' => 30,
						'default' => ''
					),
				),
				'submit' => array('title' => $this->l('Save'), 'class' => 'button'),
			),
		);
	}
}
