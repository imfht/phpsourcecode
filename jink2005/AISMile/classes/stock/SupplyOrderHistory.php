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
 * @since 1.5.0
 */
class SupplyOrderHistoryCore extends ObjectModel
{
	/**
	 * @var int Supply order Id
	 */
	public $id_supply_order;

	/**
	 * @var int Employee Id
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
	 * @var int State of the supply order
	 */
	public $id_state;

	/**
	 * @var string Date
	 */
	public $date_add;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'supply_order_history',
		'primary' => 'id_supply_order_history',
		'fields' => array(
			'id_supply_order' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_employee' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'employee_firstname' => array('type' => self::TYPE_STRING, 'validate' => 'isName'),
			'employee_lastname' => 	array('type' => self::TYPE_STRING, 'validate' => 'isName'),
			'id_state' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'date_add' => 			array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
		),
	);

	/**
	 * @see ObjectModel::$webserviceParameters
	 */
	protected $webserviceParameters = array(
		'objectsNodeName' => 'supply_order_histories',
		'objectNodeName' => 'supply_order_history',
		'fields' => array(
			'id_supply_order' => array('xlink_resource' => 'supply_orders'),
			'id_employee' => array('xlink_resource' => 'employees'),
			'id_state' => array('xlink_resource' => 'supply_order_states'),
		),
	);

}
