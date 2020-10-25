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
include_once(dirname(__FILE__).'/sendtoafriend.php');
include_once(dirname(__FILE__).'/../../classes/Product.php');

$module = new SendToAFriend();

if (Tools::getValue('action') == 'sendToMyFriend' && Tools::getValue('secure_key') == $module->secure_key)
{
		$friend_infos = Tools::jsonDecode(Tools::getValue('friend'));
		$friendName = "";
		$friendMail = "";
		$id_product = null;
		foreach ($friend_infos as $entry)
		{
			if ($entry->key == "friend_name")
				$friendName = $entry->value;
			else if ($entry->key == "friend_email")
				$friendMail = $entry->value;
			else if ($entry->key == "id_product")
				$id_product = $entry->value;
		}
		if (!$friendName || !$friendMail || !$id_product)
			die('0');

		/* Email generation */
		$product = new Product((int)$id_product, false, $module->context->language->id);
		$productLink = $module->context->link->getProductLink($product);
		$customer = $module->context->cookie->customer_firstname ? $module->context->cookie->customer_firstname.' '.$module->context->cookie->customer_lastname : $module->l('A friend', 'sendtoafriend_ajax');

		$templateVars = array(
			'{product}' => $product->name,
			'{product_link}' => $productLink,
			'{customer}' => $customer,
			'{name}' => Tools::safeOutput($friendName)
		);

		/* Email sending */
		if (!Mail::Send((int)$module->context->cookie->id_lang,
				'send_to_a_friend',
				sprintf(Mail::l('%1$s sent you a link to %2$s', (int)$module->context->cookie->id_lang), $customer, $product->name),
				$templateVars, $friendMail,
				null,
				($module->context->cookie->email ? $module->context->cookie->email : null),
				($module->context->cookie->customer_firstname ? $module->context->cookie->customer_firstname.' '.$module->context->cookie->customer_lastname : null),
				null,
				null,
				dirname(__FILE__).'/mails/'))
			die('0');
		die('1');
}
die('0');
