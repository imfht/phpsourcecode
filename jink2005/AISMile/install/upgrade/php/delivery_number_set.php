<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function delivery_number_set()
{
	Configuration::loadConfiguration();
	$number = 1;

	// Update each order with a number
	$result = Db::getInstance()->executeS('
	SELECT id_order
	FROM '._DB_PREFIX_.'orders
	ORDER BY id_order');
	foreach ($result as $row)
	{
		$order = new Order((int)($row['id_order']));
		$history = $order->getHistory(false);
		foreach ($history as $row2)
		{
			$oS = new OrderState((int)($row2['id_order_state']), Configuration::get('PS_LANG_DEFAULT'));
			if ($oS->delivery)
			{
				Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'orders SET delivery_number = '.(int)($number++).', `delivery_date` = `date_add` WHERE id_order = '.(int)($order->id));
				break ;
			}
		}
	}
	// Add configuration var
	Configuration::updateValue('PS_DELIVERY_NUMBER', (int)($number));
}

