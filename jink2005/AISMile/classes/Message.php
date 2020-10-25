<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class MessageCore extends ObjectModel
{
	public $id;

	/** @var string message content */
	public $message;

	/** @var integer Cart ID (if applicable) */
	public $id_cart;

	/** @var integer Order ID (if applicable) */
	public $id_order;

	/** @var integer Customer ID (if applicable) */
	public $id_customer;

	/** @var integer Employee ID (if applicable) */
	public $id_employee;

	/** @var boolean Message is not displayed to the customer */
	public $private;

	/** @var string Object creation date */
	public $date_add;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'message',
		'primary' => 'id_message',
		'fields' => array(
			'message' => 		array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 1600),
			'id_cart' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_order' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_customer' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_employee' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'private' => 		array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'date_add' => 		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);

	/**
	  * Return the last message from cart
	  *
	  * @param integer $id_cart Cart ID
	  * @return array Message
	  */
	public static function getMessageByCartId($id_cart)
	{
		return Db::getInstance()->getRow('
			SELECT *
			FROM `'._DB_PREFIX_.'message`
			WHERE `id_cart` = '.(int)$id_cart
		);
	}

	/**
	  * Return messages from Order ID
	  *
	  * @param integer $id_order Order ID
	  * @param boolean $private return WITH private messages
	  * @return array Messages
	  */
	public static function getMessagesByOrderId($id_order, $private = false, Context $context = null)
	{
	 	if (!Validate::isBool($private))
	 		die(Tools::displayError());

		if (!$context)
			$context = Context::getContext();

		return Db::getInstance()->executeS('
			SELECT m.*, c.`firstname` AS cfirstname, c.`lastname` AS clastname, e.`firstname` AS efirstname, e.`lastname` AS elastname,
			(COUNT(mr.id_message) = 0 AND m.id_customer != 0) AS is_new_for_me
			FROM `'._DB_PREFIX_.'message` m
			LEFT JOIN `'._DB_PREFIX_.'customer` c ON m.`id_customer` = c.`id_customer`
			LEFT JOIN `'._DB_PREFIX_.'message_readed` mr
				ON mr.`id_message` = m.`id_message`
				AND mr.`id_employee` = '.(isset($context->employee) ? (int)$context->employee->id : '\'\'').'
			LEFT OUTER JOIN `'._DB_PREFIX_.'employee` e ON e.`id_employee` = m.`id_employee`
			WHERE id_order = '.(int)$id_order.'
			'.(!$private ? ' AND m.`private` = 0' : '').'
			GROUP BY m.id_message
			ORDER BY m.date_add DESC
		');
	}

	/**
	  * Return messages from Cart ID
	  *
	  * @param integer $id_order Order ID
	  * @param boolean $private return WITH private messages
	  * @return array Messages
	  */
	public static function getMessagesByCartId($id_cart, $private = false, Context $context = null)
	{
	 	if (!Validate::isBool($private))
	 		die(Tools::displayError());

		if (!$context)
			$context = Context::getContext();

		return Db::getInstance()->executeS('
			SELECT m.*, c.`firstname` AS cfirstname, c.`lastname` AS clastname, e.`firstname` AS efirstname, e.`lastname` AS elastname,
			(COUNT(mr.id_message) = 0 AND m.id_customer != 0) AS is_new_for_me
			FROM `'._DB_PREFIX_.'message` m
			LEFT JOIN `'._DB_PREFIX_.'customer` c ON m.`id_customer` = c.`id_customer`
			LEFT JOIN `'._DB_PREFIX_.'message_readed` mr ON (mr.id_message = m.id_message AND mr.id_employee = '.(int)$context->employee->id.')
			LEFT OUTER JOIN `'._DB_PREFIX_.'employee` e ON e.`id_employee` = m.`id_employee`
			WHERE id_cart = '.(int)$id_cart.'
			'.(!$private ? ' AND m.`private` = 0' : '').'
			GROUP BY m.id_message
			ORDER BY m.date_add DESC
		');
	}

	/**
	  * Registered a message 'readed'
	  *
	  * @param integer $id_message Message ID
	  * @param integer $id_emplyee Employee ID
	  */
	public static function markAsReaded($id_message, $id_employee)
	{
	 	if (!Validate::isUnsignedId($id_message) || !Validate::isUnsignedId($id_employee))
	 		die(Tools::displayError());

		$result = Db::getInstance()->execute('
			INSERT INTO '._DB_PREFIX_.'message_readed (id_message , id_employee , date_add) VALUES
			('.(int)$id_message.', '.(int)$id_employee.', NOW());
		');
		return $result;
	}
}


