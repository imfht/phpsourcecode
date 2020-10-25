<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

if (!defined('_MB_VERSION_'))
	exit;

class Blockmyaccountfooter extends Module
{
	public function __construct()
	{
		$this->name = 'blockmyaccountfooter';
		$this->tab = 'front_office_features';
		$this->version = '1.2';
		$this->author = 'MileBiz';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('My Account block on footer');
		$this->description = $this->l('Displays a block with links relative to user account.');
	}

	public function install()
	{
		if (!$this->addMyAccountBlockHook() || !parent::install() || !$this->registerHook('footer') || !$this->registerHook('header'))
			return false;
		return true;
	}

	public function uninstall()
	{
		return parent::uninstall() && $this->removeMyAccountBlockHook();
	}

	public function hookLeftColumn($params)
	{
		global $smarty;
		
		if (!$params['cookie']->isLogged())
			return false;
		$smarty->assign(array(
			'voucherAllowed' => (int)(Configuration::get('PS_VOUCHERS')),
			'returnAllowed' => (int)(Configuration::get('PS_ORDER_RETURN')),
			'HOOK_BLOCK_MY_ACCOUNT' => Hook::exec('displayMyAccountBlock')
		));
		return $this->display(__FILE__, $this->name.'.tpl');
	}

	public function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}

	private function addMyAccountBlockHook()
	{
		return Db::getInstance()->execute('INSERT IGNORE INTO `'._DB_PREFIX_.'hook` (`name`, `title`, `description`, `position`) VALUES (\'displayMyAccountBlockfooter\', \'My account block\', \'Display extra informations inside the "my account" block\', 1)');
	}

	private function removeMyAccountBlockHook()
	{
		return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'hook` WHERE `name` = \'displayMyAccountBlockfooter\'');
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blockmyaccount.css', 'all');
	}

	public function hookFooter($params)
	{
		global $smarty;
		
		$smarty->assign(array(
			'voucherAllowed' => (int)(Configuration::get('PS_VOUCHERS')),
			'returnAllowed' => (int)(Configuration::get('PS_ORDER_RETURN')),
			'HOOK_BLOCK_MY_ACCOUNT' => Hook::exec('displayMyAccountBlock')
		));
		return $this->display(__FILE__, $this->name.'.tpl');
	}
}
