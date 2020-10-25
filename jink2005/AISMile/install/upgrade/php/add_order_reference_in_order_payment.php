<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function add_order_reference_in_order_payment()
{
	$res = true;
	$payments = Db::getInstance()->executeS('
	SELECT op.id_order_payment, o.reference
	FROM `'._DB_PREFIX_.'order_payment` op
	INNER JOIN `'._DB_PREFIX_.'orders` o
	ON o.id_order = op.id_order');
	
	if (!is_array($payments))
		return false;
	
	$errors = array();
	
	
	// Populate "order_reference"
	foreach ($payments as $payment)
	{
		$res = Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'order_payment`
		SET order_reference = \''.$payment['reference'].'\'
		WHERE id_order_payment = '.(int)$payment['id_order_payment']);
		
		if (!$res)
			$errors[] = Db::getInstance()->getMsgError();
	}
	
	if (count($errors))
		return array('error' => true, 'msg' => implode('<br/>', $errors));

	// Get lines to merge (with multishipping on, durring the payment one line was added by order, only one is necessary by cart)
	$duplicate_lines = Db::getInstance()->executeS('
	SELECT GROUP_CONCAT(id_order_payment) as id_order_payments
	FROM `'._DB_PREFIX_.'order_payment`
	GROUP BY order_reference, date_add
	HAVING COUNT(*) > 1');
	
	if (!is_array($duplicate_lines))
		return false;

	$order_payments_to_remove = array();
	foreach ($duplicate_lines as $order_payments)
	{
		$order_payments_array = explode(',', $order_payments['id_order_payments']);
		// Remove the first item (we want to keep one line)
		$id_order_payment_keep = array_shift($order_payments_array);
		
		$res = Db::getInstance()->execute('
		UPDATE `'._DB_PREFIX_.'order_invoice_payment`
		SET id_order_payement = '.(int)$id_order_payment_keep.'
		WHERE id_order_payment IN ('.implode(',', $order_payments_array).')');
		
		$order_payments_to_remove = array_merge($order_payments_to_remove, $order_payments_array);
	}
	// Remove the duplicate lines (because of the multishipping)
	if (count($order_payments_to_remove))
		$res = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'order_payment` WHERE id_order_payment IN ('.implode(',', $order_payments_to_remove).')');
	
	if (!$res)
		return array('errors' => true, 'msg' =>  Db::getInstance()->getMsgError());
	
	return true;
}
