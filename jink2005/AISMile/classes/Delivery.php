<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class DeliveryCore extends ObjectModel
{
	/** @var integer */
	public $id_delivery;

	/** @var int **/
	public $id_shop;

	/** @var int **/
	public $id_shop_group;

	/** @var integer */
	public $id_carrier;

	/** @var integer */
	public $id_range_price;

	/** @var integer */
	public $id_range_weight;

	/** @var integer */
	public $id_zone;

	/** @var float */
	public $price;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'delivery',
		'primary' => 'id_delivery',
		'fields' => array(
			'id_carrier' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_range_price' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_range_weight' =>array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_zone' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_shop' => 		array('type' => self::TYPE_INT),
			'id_shop_group' => 	array('type' => self::TYPE_INT),
			'price' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
		),
	);

	protected $webserviceParameters = array(
		'objectsNodeName' => 'deliveries',
		'fields' => array(
			'id_carrier' => array('xlink_resource' => 'carriers'),
			'id_range_price' => array('xlink_resource' => 'price_ranges'),
			'id_range_weight' => array('xlink_resource' => 'weight_ranges'),
			'id_zone' => array('xlink_resource' => 'zones'),
		)
	);

	public function getFields()
	{
		$fields = parent::getFields();

		// @todo add null management in definitions
		if ($this->id_shop)
			$fields['id_shop'] = (int)$this->id_shop;
		else
			$fields['id_shop'] = null;

		if ($this->id_shop_group)
			$fields['id_shop_group'] = (int)$this->id_shop_group;
		else
			$fields['id_shop_group'] = null;

		return $fields;
	}
}

