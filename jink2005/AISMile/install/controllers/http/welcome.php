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
 * Step 1 : display agrement form
 */
class InstallControllerHttpWelcome extends InstallControllerHttp
{
	/**
	 * Process welcome form
	 *
	 * @see InstallAbstractModel::process()
	 */
	public function processNextStep()
	{
		$this->session->licence_agrement = Tools::getValue('licence_agrement');
		$this->session->configuration_agrement = Tools::getValue('configuration_agrement');
	}

	/**
	 * Licence agrement must be checked to validate this step
	 *
	 * @see InstallAbstractModel::validate()
	 */
	public function validate()
	{
		return $this->session->licence_agrement;
	}

	/**
	 * Change language
	 */
	public function process()
	{
		if (Tools::getValue('language'))
		{
			$this->session->lang = Tools::getValue('language');
			$this->redirect('welcome');
		}
	}

	/**
	 * Display welcome step
	 */
	public function display()
	{
		$this->can_upgrade = false;
		if (file_exists(_PS_ROOT_DIR_.'/config/settings.inc.php'))
		{
			@include_once(_PS_ROOT_DIR_.'/config/settings.inc.php');
			if (version_compare(_MB_VERSION_, _MB_INSTALL_VERSION_, '<'))
			{
				$this->can_upgrade = true;
				$this->ps_version = _MB_VERSION_;
			}
		}

		$this->displayTemplate('welcome');
	}
}
