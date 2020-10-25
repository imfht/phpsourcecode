<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/* SSL Management */
$useSSL = true;

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include_once(dirname(__FILE__).'/mailalerts.php');

// Instance of module class for translations
$module = new MailAlerts();

$errors = array();

if ($cookie->isLogged())
{
	if (Tools::getValue('action') == 'delete')
	{
		$id_customer = (int)($cookie->id_customer);
		if (!$id_product = (int)(Tools::getValue('id_product')))
			$errors[] = $module->l('You must have a product to delete an alert.', 'mailalerts-account');
		$id_product_attribute = (int)(Tools::getValue('id_product_attribute'));
		$customer = new Customer((int)($id_customer));
		MailAlerts::deleteAlert((int)($id_customer), strval($customer->email), (int)($id_product), (int)($id_product_attribute));
	}
	$this->context->smarty->assign('mailAlerts', MailAlert::getProductsAlerts((int)($cookie->id_customer), (int)($cookie->id_lang)));
}
else
	$errors[] = $module->l('You must be logged in to manage your alerts.', 'mailalerts-account');

$this->context->smarty->assign(array(
	'id_customer' => (int)($cookie->id_customer),
	'errors' => $errors
));

if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/mailalerts/myalerts.tpl'))
	$smarty->display(_PS_THEME_DIR_.'modules/mailalerts/myalerts.tpl');
elseif (Tools::file_exists_cache(dirname(__FILE__).'/myalerts.tpl'))
	$smarty->display(dirname(__FILE__).'/myalerts.tpl');
else
	echo $module->l('No template found', 'mailalerts-account');

include(dirname(__FILE__).'/../../footer.php');
