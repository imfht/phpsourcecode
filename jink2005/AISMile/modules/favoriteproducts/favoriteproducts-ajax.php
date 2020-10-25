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
 * @deprecated 1.5.0 This file is deprecated, use moduleFrontController instead
 */

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/FavoriteProduct.php');

if (Tools::getValue('action') && Tools::getValue('id_product') && Context::getContext()->cookie->id_customer)
{
	if (Tools::getValue('action') == 'remove')
	{
		// check if product exists
		$product = new Product((int)Tools::getValue('id_product'));
		if (!Validate::isLoadedObject($product))
			die('0');
		$favorite_product = FavoriteProduct::getFavoriteProduct((int)Context::getContext()->cookie->id_customer, (int)$product->id);
		if ($favorite_product)
			if ($favorite_product->delete())
				die('0');
	}
	elseif (Tools::getValue('action') == 'add')
	{
		$product = new Product((int)Tools::getValue('id_product'));
		// check if product exists
		if (!Validate::isLoadedObject($product)
			|| FavoriteProduct::isCustomerFavoriteProduct((int)Context::getContext()->cookie->id_customer, (int)$product->id))
			die('1');
		$favorite_product = new FavoriteProduct();
		$favorite_product->id_product = $product->id;
		$favorite_product->id_customer = (int)Context::getContext()->cookie->id_customer;
		$favorite_product->id_shop = (int)Context::getContext()->shop->id;
		if ($favorite_product->add())
			die('0');
	}
}

die('1');

