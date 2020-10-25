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
include_once(dirname(__FILE__).'/WishList.php');

$context = Context::getContext();
$errors = array();

Tools::displayFileAsDeprecated();

// Instance of module class for translations
$module = new BlockWishList();

if ($context->customer->isLogged())
{
	$add = Tools::getIsset('add');
	$add = (empty($add) === false ? 1 : 0);
	$delete = Tools::getIsset('deleted');
	$delete = (empty($delete) === false ? 1 : 0);
	$id_wishlist = Tools::getValue('id_wishlist');
	if (Tools::isSubmit('submitWishlist'))
	{
		if (Configuration::get('PS_TOKEN_ACTIVATED') == 1 AND
			strcmp(Tools::getToken(), Tools::getValue('token')))
			$errors[] = $module->l('Invalid token', 'mywishlist');
		if (!sizeof($errors))
		{
			$name = Tools::getValue('name');
			if (empty($name))
				$errors[] = $module->l('You must specify a name.', 'mywishlist');
			if (WishList::isExistsByNameForUser($name))
				$errors[] = $module->l('This name is already used by another list.', 'mywishlist');
			
			if(!sizeof($errors))
			{
				$wishlist = new WishList();
				$wishlist->name = $name;
				$wishlist->id_customer = (int)$context->customer->id;
                $wishlist->id_shop = $context->shop->id;
                $wishlist->id_shop_group = $context->shop->id_shop_group;
				list($us, $s) = explode(' ', microtime());
				srand($s * $us);
				$wishlist->token = strtoupper(substr(sha1(uniqid(rand(), true)._COOKIE_KEY_.$context->customer->id), 0, 16));
				$wishlist->add();
				Mail::Send($context->language->id, 'wishlink', Mail::l('Your wishlist\'s link', $context->language->id), 
					array(
					'{wishlist}' => $wishlist->name,
					'{message}' => Tools::getProtocol().htmlentities($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/blockwishlist/view.php?token='.$wishlist->token),
					$context->customer->email, $context->customer->firstname.' '.$context->customer->lastname, NULL, strval(Configuration::get('PS_SHOP_NAME')), NULL, NULL, dirname(__FILE__).'/mails/');
			}
		}
	}
	else if ($add)
		WishList::addCardToWishlist($context->customer->id, Tools::getValue('id_wishlist'), $context->language->id);
	else if ($delete AND empty($id_wishlist) === false)
	{
		$wishlist = new WishList((int)($id_wishlist));
		if (Validate::isLoadedObject($wishlist))
			$wishlist->delete();
		else
			$errors[] = $module->l('Cannot delete this wishlist', 'mywishlist');
	}
	$context->smarty->assign('wishlists', WishList::getByIdCustomer($context->customer->id));
	$context->smarty->assign('nbProducts', WishList::getInfosByIdCustomer($context->customer->id));
}
else
{
	Tools::redirect('index.php?controller=authentication&back=modules/blockwishlist/mywishlist.php');
}

$context->smarty->assign(array(
	'id_customer' => (int)$context->customer->id,
	'errors' => $errors
));

if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/blockwishlist/mywishlist.tpl'))
	$context->smarty->display(_PS_THEME_DIR_.'modules/blockwishlist/mywishlist.tpl');
elseif (Tools::file_exists_cache(dirname(__FILE__).'/views/templates/front/mywishlist.tpl'))
	$context->smarty->display(dirname(__FILE__).'/views/templates/front/mywishlist.tpl');
else
	echo $module->l('No template found', 'mywishlist');

include(dirname(__FILE__).'/../../footer.php');


