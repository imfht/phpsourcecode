<?php
/**
 * MILEBIZ Ã×ÀÖÉÌ³Ç
 * ============================================================================
 * °æÈ¨ËùÓĞ 2011-20__ Ã×ÀÖÍø¡£
 * ÍøÕ¾µØÖ·: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

if (!defined('_MB_VERSION_'))
	exit;

class LoyaltyStateModule extends ObjectModel
{
	public $name;
	public $id_order_state;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'loyalty_state',
		'primary' => 'id_loyalty_state',
		'multilang' => true,
		'fields' => array(
			'id_order_state' =>	array('type' => self::TYPE_INT, 'validate' => 'isInt'),

			// Lang fields
			'name' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
		),
	);

	public static function getDefaultId() { return 1; }
	public static function getValidationId() { return 2; }
	public static function getCancelId() { return 3; }
	public static function getConvertId() { return 4; }
	public static function getNoneAwardId() { return 5; }

	public static function insertDefaultData()
	{
		$loyaltyModule = new Loyalty();
		$languages = Language::getLanguages();
		
		$defaultTranslations = array('default' => array('id_loyalty_state' => (int)LoyaltyStateModule::getDefaultId(), 'default' => $loyaltyModule->getL('Awaiting validation'), 'en' => 'Awaiting validation', 'fr' => 'En attente de validation'));
		$defaultTranslations['validated'] = array('id_loyalty_state' => (int)LoyaltyStateModule::getValidationId(), 'id_order_state' => Configuration::get('PS_OS_DELIVERED'), 'default' => $loyaltyModule->getL('Available'), 'en' => 'Available', 'fr' => 'Disponible');
		$defaultTranslations['cancelled'] = array('id_loyalty_state' => (int)LoyaltyStateModule::getCancelId(), 'id_order_state' => Configuration::get('PS_OS_CANCELED'), 'default' => $loyaltyModule->getL('Cancelled'), 'en' => 'Cancelled', 'fr' => 'AnnulÃ©s');
		$defaultTranslations['converted'] = array('id_loyalty_state' => (int)LoyaltyStateModule::getConvertId(), 'default' => $loyaltyModule->getL('Already converted'), 'en' => 'Already converted', 'fr' => 'DÃ©jÃ  convertis');
		$defaultTranslations['none_award'] = array('id_loyalty_state' => (int)LoyaltyStateModule::getNoneAwardId(), 'default' => $loyaltyModule->getL('Unavailable on discounts'), 'en' => 'Unavailable on discounts', 'fr' => 'Non disponbile sur produits remisÃ©s');
		
		foreach ($defaultTranslations AS $loyaltyState)
		{
			$state = new LoyaltyStateModule((int)$loyaltyState['id_loyalty_state']);
			if (isset($loyaltyState['id_order_state']))
				$state->id_order_state = (int)$loyaltyState['id_order_state'];
			$state->name[(int)Configuration::get('PS_LANG_DEFAULT')] = $loyaltyState['default'];
			foreach ($languages AS $language)
				if (isset($loyaltyState[$language['iso_code']]))
					$state->name[(int)$language['id_lang']] = $loyaltyState[$language['iso_code']];
			$state->save();
		}

		return true;
	}
}
