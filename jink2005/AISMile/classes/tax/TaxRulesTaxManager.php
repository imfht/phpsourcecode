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
 * @since 1.5.0.1
 */
class TaxRulesTaxManagerCore implements TaxManagerInterface
{
	public $address;
	public $type;
	public $tax_calculator;

	protected static $cache_tax_calculator;


	/**
	 * 
	 * @param Address $address
	 * @param mixed An additional parameter for the tax manager (ex: tax rules id for TaxRuleTaxManager)
	 */
	public function __construct(Address $address, $type)
	{
		$this->address = $address;
		$this->type = $type;
	}

	/**
	* Returns true if this tax manager is available for this address
	*
	* @return boolean
	*/
	public static function isAvailableForThisAddress(Address $address)
	{
		return true; // default manager, available for all addresses
	}

	/**
	* Return the tax calculator associated to this address
	*
	* @return TaxCalculator
	*/
	public function getTaxCalculator()
	{
		if (isset($this->tax_calculator))
			return $this->tax_calculator;

		$taxes = array();
		if (!Configuration::get('PS_TAX'))
			return new TaxCalculator($taxes);

		$postcode = 0;
		if (!empty($this->address->postcode))
			$postcode = $this->address->postcode;

		if (!isset(self::$cache_tax_calculator[$postcode.'-'.$this->type]))
		{
			$rows = Db::getInstance()->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'tax_rule`
			WHERE `id_country` = '.(int)$this->address->id_country.'
			AND `id_tax_rules_group` = '.(int)$this->type.'
			AND `id_state` IN (0, '.(int)$this->address->id_state.')
			AND (\''.pSQL($postcode).'\' BETWEEN `zipcode_from` AND `zipcode_to` OR `zipcode_from` = 0 OR `zipcode_from` = \''.pSQL($postcode).'\')
			ORDER BY `zipcode_from` DESC, `zipcode_to` DESC, `id_state` DESC, `id_country` DESC');

			$behavior = 0;
			$first_row = true;

			foreach ($rows as $row)
			{
				$tax = new Tax((int)$row['id_tax']);

				$taxes[] = $tax;

				// the applied behavior correspond to the most specific rules
				if ($first_row)
				{
					$behavior = $row['behavior'];
					$first_row = false;
				}

				if ($row['behavior'] == 0)
					 break;
			}

			self::$cache_tax_calculator[$postcode.'-'.$this->type] = new TaxCalculator($taxes, $behavior);
		}

		return self::$cache_tax_calculator[$postcode.'-'.$this->type];
	}
}

