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
class WarehouseProductLocationCore extends ObjectModel
{
	/**
	 * @var int product ID
	 * */
	public $id_product;

	/**
	 * @var int product attribute ID
	 * */
	public $id_product_attribute;

	/**
	 * @var int warehouse ID
	 * */
	public $id_warehouse;

	/**
	 * @var string location of the product
	 * */
	public $location;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'warehouse_product_location',
		'primary' => 'id_warehouse_product_location',
		'fields' => array(
			'location' => 				array('type' => self::TYPE_STRING, 'validate' => 'isReference', 'size' => 64),
			'id_product' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_product_attribute' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_warehouse' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
		),
	);

	/**
	 * @see ObjectModel::$webserviceParameters
	 */
 	protected $webserviceParameters = array(
 		'fields' => array(
 			'id_product' => array('xlink_resource' => 'products'),
 			'id_product_attribute' => array('xlink_resource' => 'combinations'),
 			'id_warehouse' => array('xlink_resource' => 'warehouses'),
 		),
 		'hidden_fields' => array(
 		),
 	);

	/**
	 * For a given product and warehouse, gets the location
	 *
	 * @param int $id_product product ID
	 * @param int $id_product_attribute product attribute ID
	 * @param int $id_warehouse warehouse ID
	 * @return string $location Location of the product
	 */
	public static function getProductLocation($id_product, $id_product_attribute, $id_warehouse)
	{
		// build query
		$query = new DbQuery();
		$query->select('wpl.location');
		$query->from('warehouse_product_location', 'wpl');
		$query->where('wpl.id_product = '.(int)$id_product.'
			AND wpl.id_product_attribute = '.(int)$id_product_attribute.'
			AND wpl.id_warehouse = '.(int)$id_warehouse
		);

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	/**
	 * For a given product and warehouse, gets the WarehouseProductLocation corresponding ID
	 *
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @param int $id_supplier
	 * @return int $id_warehouse_product_location ID of the WarehouseProductLocation
	 */
	public static function getIdByProductAndWarehouse($id_product, $id_product_attribute, $id_warehouse)
	{
		// build query
		$query = new DbQuery();
		$query->select('wpl.id_warehouse_product_location');
		$query->from('warehouse_product_location', 'wpl');
		$query->where('wpl.id_product = '.(int)$id_product.'
			AND wpl.id_product_attribute = '.(int)$id_product_attribute.'
			AND wpl.id_warehouse = '.(int)$id_warehouse
		);

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
	}

	/**
	 * For a given product, gets its warehouses
	 *
	 * @param int $id_product
	 * @return Collection The type of the collection is WarehouseProductLocation
	 */
	public static function getCollection($id_product)
	{
		$collection = new Collection('WarehouseProductLocation');
		$collection->where('id_product', '=', (int)$id_product);
		return $collection;
	}
	
	public static function getProducts($id_warehouse)
	{
		return Db::getInstance()->executeS('SELECT DISTINCT id_product FROM '._DB_PREFIX_.'warehouse_product_location WHERE id_warehouse='.(int)$id_warehouse);		
	}
}