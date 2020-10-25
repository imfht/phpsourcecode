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

class BlockCurrencies extends Module
{
	public function __construct()
	{
		$this->name = 'blockcurrencies';
		$this->tab = 'front_office_features';
		$this->version = 0.1;
		$this->author = 'MileBiz';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Currency block');
		$this->description = $this->l('Adds a block for selecting a currency.');
	}

	public function install()
	{
		return parent::install() && $this->registerHook('top') && $this->registerHook('header');
	}

	private function _prepareHook($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return false;

		if (!count(Currency::getCurrencies()))
			return false;

		$this->smarty->assign('blockcurrencies_sign', $this->context->currency->sign);
	
		return true;
	}

	/**
	* Returns module content for header
	*
	* @param array $params Parameters
	* @return string Content
	*/
	public function hookTop($params)
	{
		if ($this->_prepareHook($params))
			return $this->display(__FILE__, 'blockcurrencies.tpl');
	}

	public function hookHeader($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;
		$this->context->controller->addCSS(($this->_path).'blockcurrencies.css', 'all');
	}
}


