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
class FavoriteproductsAccountModuleFrontController extends ModuleFrontController
{
	public $ssl = true;

	public function init()
	{
		parent::init();

		require_once($this->module->getLocalPath().'FavoriteProduct.php');
	}

	public function initContent()
	{
		parent::initContent();

		if (!Context::getContext()->customer->isLogged())
			Tools::redirect('index.php?controller=authentication&redirect=module&module=favoriteproducts&action=account');

		if (Context::getContext()->customer->id)
		{
			$this->context->smarty->assign('favoriteProducts', FavoriteProduct::getFavoriteProducts((int)Context::getContext()->customer->id, (int)Context::getContext()->language->id));
			$this->setTemplate('favoriteproducts-account.tpl');
		}
	}
}