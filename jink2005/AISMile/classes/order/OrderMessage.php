<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class OrderMessageCore extends ObjectModel
{
	/** @var string name name */
	public $name;

	/** @var string message content */
	public $message;

	/** @var string Object creation date */
	public $date_add;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'order_message',
		'primary' => 'id_order_message',
		'multilang' => true,
		'fields' => array(
			'date_add' => 	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),

			// Lang fields
			'name' => 		array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
			'message' => 	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isMessage', 'required' => true, 'size' => 1200),
		),
	);

	protected $webserviceParameters = array(
			'fields' => array(
			'id' => array('sqlId' => 'id_discount_type', 'xlink_resource' => 'order_message_lang'),
			'date_add' => array('sqlId' => 'date_add')
		)
	);

	public static function getOrderMessages($id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT om.id_order_message, oml.name, oml.message
		FROM '._DB_PREFIX_.'order_message om
		LEFT JOIN '._DB_PREFIX_.'order_message_lang oml ON (oml.id_order_message = om.id_order_message)
		WHERE oml.id_lang = '.(int)$id_lang.'
		ORDER BY name ASC');
	}
}
