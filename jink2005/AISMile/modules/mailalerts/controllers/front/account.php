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
 * @since 1.5.0
 */
class MailalertsAccountModuleFrontController extends ModuleFrontController
{
	public function init()
	{
		parent::init();

		require_once($this->module->getLocalPath().'MailAlert.php');
	}

	public function initContent()
	{
		parent::initContent();

		if (!Context::getContext()->customer->isLogged())
			Tools::redirect('index.php?controller=authentication&redirect=module&module=mailalerts&action=account');

		if (Context::getContext()->customer->id)
		{
			$this->context->smarty->assign('id_customer', Context::getContext()->customer->id);
			$this->context->smarty->assign('mailAlerts', MailAlert::getMailAlerts((int)Context::getContext()->customer->id, (int)Context::getContext()->language->id));
			
			$this->setTemplate('mailalerts-account.tpl');
		}
	}
}