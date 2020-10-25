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
* A TaxManager define a way to retrieve tax.
*/
interface TaxManagerInterface
{
	/**
	* This method determine if the tax manager is available for the specified address.
	*
	* @param Address $address
	* @param string $type
	*
	* @return TaxManager
   */
	public static function isAvailableForThisAddress(Address $address);

	/**
	* Return the tax calculator associated to this address
	*
	* @return TaxCalculator
	*/
	public function getTaxCalculator();
}

