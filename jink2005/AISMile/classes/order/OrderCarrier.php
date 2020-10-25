<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class OrderCarrierCore extends ObjectModel
{
	/** @var integer */
	public $id_order_carrier;

	/** @var integer */
	public $id_order;

	/** @var integer */
	public $id_carrier;

	/** @var integer */
	public $id_order_invoice;

	/** @var float */
	public $weight;

	/** @var float */
	public $shipping_cost_tax_excl;

	/** @var float */
	public $shipping_cost_tax_incl;

	/** @var integer */
	public $tracking_number;

	/** @var string Object creation date */
	public $date_add;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'order_carrier',
		'primary' => 'id_order_carrier',
		'fields' => array(
			'id_order' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_carrier' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_order_invoice' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'weight' => 				array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
			'shipping_cost_tax_excl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
			'shipping_cost_tax_incl' => array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
			'tracking_number' => 		array('type' => self::TYPE_STRING, 'validate' => 'isTrackingNumber'),
			'date_add' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);

	protected $webserviceParameters = array(
		'fields' => array(
			'id_order' => array('xlink_resource' => 'orders'),
			'id_carrier' => array('xlink_resource' => 'carriers'),
		),
	);
}
