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
 *
 * @deprecated 1.5.0.1
 * @see OrderPaymentCore
 *
 */
class PaymentCCCore extends OrderPayment
{
	public $id_order;
	public $id_currency;
	public $amount;
	public $transaction_id;
	public $card_number;
	public $card_brand;
	public $card_expiration;
	public $card_holder;
	public $date_add;

	protected	$fieldsRequired = array('id_currency', 'amount');
	protected	$fieldsSize = array('transaction_id' => 254, 'card_number' => 254, 'card_brand' => 254, 'card_expiration' => 254, 'card_holder' => 254);
	protected	$fieldsValidate = array(
		'id_order' => 'isUnsignedId', 'id_currency' => 'isUnsignedId', 'amount' => 'isPrice',
		'transaction_id' => 'isAnything', 'card_number' => 'isAnything', 'card_brand' => 'isAnything', 'card_expiration' => 'isAnything', 'card_holder' => 'isAnything');

	public static $definition = array(
		'table' => 'payment_cc',
		'primary' => 'id_payment_cc',
	);

	/**
	 * @deprecated 1.5.0.2
	 * @see OrderPaymentCore
	 */
	public function getFields()
	{
		Tools::displayAsDeprecated();
		return parent::getFields();
	}

	/**
	 * @deprecated 1.5.0.2
	 * @see OrderPaymentCore
	 */
	public function add($autodate = true, $nullValues = false)
	{
		Tools::displayAsDeprecated();
		return parent::add($autodate, $nullValues);
	}

	/**
	* Get the detailed payment of an order
	* @param int $id_order
	* @return array
	* @deprecated 1.5.0.1
	* @see OrderPaymentCore
	*/
	public static function getByOrderId($id_order)
	{
		Tools::displayAsDeprecated();
		$order = new Order($id_order);
		return OrderPayment::getByOrderReference($order->reference);
	}
}
