<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class OrderCartRuleCore extends ObjectModel
{
	/** @var integer */
	public $id_order_cart_rule;

	/** @var integer */
	public $id_order;

	/** @var integer */
	public $id_cart_rule;

	/** @var integer */
	public $id_order_invoice;

	/** @var string */
	public $name;

	/** @var float value (tax incl.) of voucher */
	public $value;

	/** @var float value (tax excl.) of voucher */
	public $value_tax_excl;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'order_cart_rule',
		'primary' => 'id_order_cart_rule',
		'fields' => array(
			'id_order' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_cart_rule' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_order_invoice' =>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'name' => 				array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true),
			'value' => 				array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
			'value_tax_excl' => 	array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true)
		)
	);

	protected $webserviceParameters = array(
		'fields' => array(
			'id_order' => array('xlink_resource' => 'orders'),
		),
	);
}

