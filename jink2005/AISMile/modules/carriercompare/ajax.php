<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
require_once(dirname(__FILE__).'/carriercompare.php');

$carrierCompare = new CarrierCompare();

switch (Tools::getValue('method'))
{
	case 'getStates':
		if (!(int)Tools::getValue('id_country'))
			exit;
		die(Tools::jsonEncode($carrierCompare->getStatesByIdCountry((int)Tools::getValue('id_country'))));
		break;
	case 'getCarriers':
		die(Tools::jsonEncode($carrierCompare->getCarriersListByIdZone((int)Tools::getValue('id_country'), (int)Tools::getValue('id_state', 0), Tools::safeOutput(Tools::getValue('zipcode', 0)))));
		break;
	case 'saveSelection':
		$errors = $carrierCompare->saveSelection((int)Tools::getValue('id_country'), (int)Tools::getValue('id_state', 0), Tools::getValue('zipcode', 0), (int)Tools::getValue('id_carrier', 0));
		die(Tools::jsonEncode($errors));
		break;
	default:
		exit;
}
exit;
