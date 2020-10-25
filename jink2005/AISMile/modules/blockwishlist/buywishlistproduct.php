<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
require_once(dirname(__FILE__).'/WishList.php');


$error = '';

// Instance of module class for translations
$module = new BlockWishList();

$token = Tools::getValue('token');
$id_product = (int)Tools::getValue('id_product');
$id_product_attribute = (int)Tools::getValue('id_product_attribute');
if (Configuration::get('PS_TOKEN_ENABLE') == 1 && strcmp(Tools::getToken(false), Tools::getValue('static_token')))
	$error = $module->l('Invalid token', 'buywishlistproduct');

if (!strlen($error) &&
	empty($token) === false &&
	empty($id_product) === false)
{
	$wishlist = WishList::getByToken($token);
	if ($wishlist !== false)
		WishList::addBoughtProduct($wishlist['id_wishlist'], $id_product, $id_product_attribute, $cart->id, 1);
}
else
	$error = $module->l('You must log in', 'buywishlistproduct');

if (empty($error) === false)
	echo $error;

