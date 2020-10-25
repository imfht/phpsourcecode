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
* @deprecated since 1.5
*/
class CountyCore extends ObjectModel
{
	public $id;
	public $name;
	public $id_state;
	public $active;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'county',
		'primary' => 'id_county',
		'fields' => array(
			'name' => 		array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
			'id_state' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'active' => 	array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
		),
	);

	protected static $_cache_get_counties = array();
	protected static $_cache_county_zipcode = array();

	const USE_BOTH_TAX = 0;
	const USE_COUNTY_TAX = 1;
	const USE_STATE_TAX = 2;

	protected	$webserviceParameters = array(
		'fields' => array(
			'id_state' => array('xlink_resource'=> 'states'),
		),
	);

	public function delete()
	{
		return true;
	}

	/**
	* @deprecated since 1.5
	*/
	public static function getCounties($id_state)
	{
		Tools::displayAsDeprecated();
		return false;
	}

	/**
	* @deprecated since 1.5
	*/
	public function getZipCodes()
	{
		Tools::displayAsDeprecated();
		return false;
	}

	/**
	* @deprecated since 1.5
	*/
	public function addZipCodes($zip_codes)
	{
		Tools::displayAsDeprecated();
		return true;
	}

	/**
	* @deprecated since 1.5
	*/
	public function removeZipCodes($zip_codes)
	{
		Tools::displayAsDeprecated();
		return true;
	}

	/**
	* @deprecated since 1.5
	*/
	public function breakDownZipCode($zip_codes)
	{
		Tools::displayAsDeprecated();
		return array(0,0);
	}

	/**
	* @deprecated since 1.5
	*/
	public static function getIdCountyByZipCode($id_state, $zip_code)
	{
		Tools::displayAsDeprecated();
		return false;
	}

	/**
	* @deprecated since 1.5
	*/
	public function isZipCodeRangePresent($zip_codes)
	{
		Tools::displayAsDeprecated();
		return false;
	}

	/**
	* @deprecated since 1.5
	*/
	public function isZipCodePresent($zip_code)
	{
		Tools::displayAsDeprecated();
		return false;
	}

	/**
	* @deprecated since 1.5
	*/
	public static function deleteZipCodeByIdCounty($id_county)
	{
		Tools::displayAsDeprecated();
		return true;
	}

	/**
	* @deprecated since 1.5
	*/
	public static function getIdCountyByNameAndIdState($name, $id_state)
	{
		Tools::displayAsDeprecated();
		return false;
	}

}

