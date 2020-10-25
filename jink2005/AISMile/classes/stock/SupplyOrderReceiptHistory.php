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
 * History of receipts
 * @since 1.5.0
 */
class SupplyOrderReceiptHistoryCore extends ObjectModel
{
	/**
	 * @var int Detail of the supply order (i.e. One particular product)
	 */
	public $id_supply_order_detail;

	/**
	 * @var int Employee
	 */
	public $id_employee;

	/**
	 * @var string The first name of the employee responsible of the movement
	 */
	public $employee_firstname;

	/**
	 * @var string The last name of the employee responsible of the movement
	 */
	public $employee_lastname;

	/**
	 * @var int State
	 */
	public $id_supply_order_state;

	/**
	 * @var int Quantity delivered
	 */
	public $quantity;

	/**
	 * @var string Date of delivery
	 */
	public $date_add;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'supply_order_receipt_history',
		'primary' => 'id_supply_order_receipt_history',
		'fields' => array(
			'id_supply_order_detail' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_supply_order_state' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_employee' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'employee_firstname' => 	array('type' => self::TYPE_STRING, 'validate' => 'isName'),
			'employee_lastname' => 		array('type' => self::TYPE_STRING, 'validate' => 'isName'),
			'quantity' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
			'date_add' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);

	/**
	 * @see ObjectModel::$webserviceParameters
	 */
	protected $webserviceParameters = array(
		'objectsNodeName' => 'supply_order_receipt_histories',
		'objectNodeName' => 'supply_order_receipt_history',
		'fields' => array(
			'id_supply_order_detail' => array('xlink_resource' => 'supply_order_details'),
			'id_employee' => array('xlink_resource' => 'employees'),
			'id_supply_order_state' => array('xlink_resource' => 'supply_order_states'),
		),
	);

}

