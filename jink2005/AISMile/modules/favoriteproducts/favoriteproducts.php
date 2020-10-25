<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class FavoriteProducts extends Module
{
	public function __construct()
	{
		$this->name = 'favoriteproducts';
		$this->tab = 'front_office_features';
		$this->version = 1.0;
		$this->author = 'MileBiz';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Favorite Products');
		$this->description = $this->l('Display a page with the customer\'s favorite products');
	}

	public function install()
	{
			if (!parent::install()
				|| !$this->registerHook('displayMyAccountBlock')
				|| !$this->registerHook('displayCustomerAccount')
				|| !$this->registerHook('displayLeftColumnProduct')
				|| !$this->registerHook('extraLeft')
				|| !$this->registerHook('displayHeader'))
					return false;

			if (!Db::getInstance()->execute('
				CREATE TABLE `'._DB_PREFIX_.'favorite_product` (
				`id_favorite_product` int(10) unsigned NOT NULL auto_increment,
				`id_product` int(10) unsigned NOT NULL,
				`id_customer` int(10) unsigned NOT NULL,
				`id_shop` int(10) unsigned NOT NULL,
				`date_add` datetime NOT NULL,
  				`date_upd` datetime NOT NULL,
				PRIMARY KEY (`id_favorite_product`))
				ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
				return false;

			return true;
	}

	public function uninstall()
	{
		if (!parent::uninstall() || !Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'favorite_product`'))
			return false;
		return true;
	}

	public function hookDisplayCustomerAccount($params)
	{
		$this->smarty->assign('in_footer', false);
		return $this->display(__FILE__, 'my-account.tpl');
	}

	public function hookDisplayMyAccountBlock($params)
	{
		$this->smarty->assign('in_footer', true);
		return $this->display(__FILE__, 'my-account.tpl');
	}

	public function hookDisplayLeftColumnProduct($params)
	{
		include_once(dirname(__FILE__).'/FavoriteProduct.php');

		$this->smarty->assign(array(
			'isCustomerFavoriteProduct' => (FavoriteProduct::isCustomerFavoriteProduct($this->context->customer->id, Tools::getValue('id_product')) ? 1 : 0),
			'isLogged' => (int)$this->context->customer->logged,
		));
		return $this->display(__FILE__, 'favoriteproducts-extra.tpl');
	}

	public function hookDisplayHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'favoriteproducts.css', 'all');
		$this->context->controller->addJS($this->_path.'favoriteproducts.js');
		return $this->display(__FILE__, 'favoriteproducts-header.tpl');
	}

}


