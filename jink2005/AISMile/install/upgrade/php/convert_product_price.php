<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/* Convert product prices from the PS < 1.3 wrong rounding system to the new 1.3 one */
function convert_product_price()
{
	$taxes = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'tax');
	$taxRates = array();
	foreach ($taxes as $data)
		$taxRates[$data['id_tax']] = (float)($data['rate']) / 100;
	$resource = DB::getInstance()->executeS('SELECT `id_product`, `price`, `id_tax` 
		FROM `'._DB_PREFIX_.'product`', false);
	if (!$resource)
		return array('error' => 1, 'msg' => Db::getInstance()->getMsgError()); // was previously die(mysql_error())

	while ($row = DB::getInstance()->nextRow($resource))
		if ($row['id_tax'])
		{
			$price = $row['price'] * (1 + $taxRates[$row['id_tax']]);
			$decimalPart = $price - (int)$price;
			if ($decimalPart < 0.000001)
			{
				$newPrice = (float)(number_format($price, 6, '.', ''));
				$newPrice = Tools::floorf($newPrice / (1 + $taxRates[$row['id_tax']]), 6);
				DB::getInstance()->execute('UPDATE `'._DB_PREFIX_.'product` SET `price` = '.$newPrice.' WHERE `id_product` = '.(int)$row['id_product']);
			}
		}
}
