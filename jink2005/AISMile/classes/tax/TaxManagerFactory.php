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
* @since 1.5
*/
class TaxManagerFactoryCore
{
	protected static $cache_tax_manager;

	/**
	* Returns a tax manager able to handle this address
	*
	* @param Address $address
	* @param string $type
	*
	* @return TaxManager
	*/
	public static function getManager(Address $address, $type)
	{
		$cache_id = TaxManagerFactory::getCacheKey($address).'-'.$type;
		if (!isset(TaxManagerFactory::$cache_tax_manager[$cache_id]))
		{
			$tax_manager = TaxManagerFactory::execHookTaxManagerFactory($address, $type);
			if (!($tax_manager instanceof TaxManagerInterface))
				$tax_manager = new TaxRulesTaxManager($address, $type);

			TaxManagerFactory::$cache_tax_manager[$cache_id] = $tax_manager;
		}

		return TaxManagerFactory::$cache_tax_manager[$cache_id];
	}

	/**
	* Check for a tax manager able to handle this type of address in the module list
	*
	* @param Address $address
	* @param string $type
	*
	* @return TaxManager
   */
	public static function execHookTaxManagerFactory(Address $address, $type)
	{
		$modules_infos = Hook::getModulesFromHook(Hook::getIdByName('taxManager'));
		$tax_manager = false;

		foreach ($modules_infos as $module_infos)
		{
			$module_instance = Module::getInstanceByName($module_infos['name']);
			if (is_callable(array($module_instance, 'hookTaxManager')))
			{
				$tax_manager = $module_instance->hookTaxManager(array(
																'address' => $address,
																'params' => $type
															));
			}

			if ($tax_manager)
				break;
		}

		return $tax_manager;
	}


	/**
	* Create a unique identifier for the address
	* @param Address
	*/
	protected static function getCacheKey(Address $address)
	{
		return $address->id_country.'-'
				.(int)$address->id_state.'-'
				.$address->postcode.'-'
				.$address->vat_number.'-'
				.$address->dni;
	}
}

