<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class AttributeCore extends ObjectModel
{
	/** @var integer Group id which attribute belongs */
	public $id_attribute_group;

	/** @var string Name */
	public $name;
	public $color;
	public $position;
	public $default;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'attribute',
		'primary' => 'id_attribute',
		'multilang' => true,
		'fields' => array(
			'id_attribute_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'color' => 				array('type' => self::TYPE_STRING, 'validate' => 'isColor'),
			'position' => 			array('type' => self::TYPE_INT, 'validate' => 'isInt'),

			// Lang fields
			'name' => 				array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 64),
		)
	);


	protected	$image_dir = _PS_COL_IMG_DIR_;

	protected $webserviceParameters = array(
		'objectsNodeName' => 'product_option_values',
		'objectNodeName' => 'product_option_value',
		'fields' => array(
			'id_attribute_group' => array('xlink_resource'=> 'product_options'),
		)
	);

	public function __construct($id = null, $id_lang = null, $id_shop = null)
	{
		$this->image_dir = _PS_COL_IMG_DIR_;

		parent::__construct($id, $id_lang, $id_shop);
	}

	public function delete()
	{
		if (!$this->hasMultishopEntries())
		{
			$result = Db::getInstance()->executeS('SELECT id_product_attribute FROM '._DB_PREFIX_.'product_attribute_combination WHERE id_attribute = '.(int)$this->id);
			foreach ($result as $row)
			{
				$combination = new Combination($row['id_product_attribute']);
				$combination->delete();
			}
		
			// Delete associated restrictions on cart rules
			CartRule::cleanProductRuleIntegrity('attributes', $this->id);

			/* Reinitializing position */
			$this->cleanPositions((int)$this->id_attribute_group);
		}
		$return = parent::delete();
		if ($return)
			Hook::exec('actionAttributeDelete', array('id_attribute' => $this->id));

		return $return;
	}

	public function update($null_values = false)
	{
		$return = parent::update($null_values);

		if ($return)
			Hook::exec('actionAttributeSave', array('id_attribute' => $this->id));

		return $return;
	}

	public function add($autodate = true, $null_values = false)
	{
		if ($this->position <= 0)
			$this->position = Attribute::getHigherPosition($this->id_attribute_group) + 1;

		$return = parent::add($autodate, $null_values);

		if ($return)
			Hook::exec('actionAttributeSave', array('id_attribute' => $this->id));

		return $return;
	}

	/**
	 * Get all attributes for a given language
	 *
	 * @param integer $id_lang Language id
	 * @param boolean $notNull Get only not null fields if true
	 * @return array Attributes
	 */
	public static function getAttributes($id_lang, $not_null = false)
	{
		if (!Combination::isFeatureActive())
			return array();

		return Db::getInstance()->executeS('
			SELECT ag.*, agl.*, a.`id_attribute`, al.`name`, agl.`name` AS `attribute_group`
			FROM `'._DB_PREFIX_.'attribute_group` ag
			LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
				ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
			LEFT JOIN `'._DB_PREFIX_.'attribute` a
				ON a.`id_attribute_group` = ag.`id_attribute_group`
			LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
				ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
			'.Shop::addSqlAssociation('attribute_group', 'ag').'
			'.Shop::addSqlAssociation('attribute', 'a').'
			'.($not_null ? 'WHERE a.`id_attribute` IS NOT NULL AND al.`name` IS NOT NULL' : '').'
			ORDER BY agl.`name` ASC, a.`position` ASC
		');
	}

	/**
	 * Get quantity for a given attribute combination
	 * Check if quantity is enough to deserve customer
	 *
	 * @param integer $id_product_attribute Product attribute combination id
	 * @param integer $qty Quantity needed
	 * @return boolean Quantity is available or not
	 */
	public static function checkAttributeQty($id_product_attribute, $qty, Shop $shop = null)
	{
		if (!$shop)
			$shop = Context::getContext()->shop;

		$result = StockAvailable::getQuantityAvailableByProduct(null, (int)$id_product_attribute, $shop->id);

		return ($result && $qty <= $result);
	}

	/**
	 * @deprecated 1.5.0, use StockAvailable::getQuantityAvailableByProduct()
	 */
	public static function getAttributeQty($id_product)
	{
		Tools::displayAsDeprecated();

		return StockAvailable::getQuantityAvailableByProduct($id_product);
	}

	/**
	 * Update array with veritable quantity
	 *
	 * @deprecated since 1.5.0
	 * @param array &$arr
	 * @return bool
	 */
	public static function updateQtyProduct(&$arr)
	{
		Tools::displayAsDeprecated();

		$id_product = (int)$arr['id_product'];
		$qty = Attribute::getAttributeQty($id_product);

		if ($qty !== false)
		{
			$arr['quantity'] = (int)$qty;
			return true;
		}

		return false;
	}

	/**
	 * Return true if attribute is color type
	 *
	 * @acces public
	 * @return bool
	 */
	public function isColorAttribute()
	{
		if (!Db::getInstance()->getRow('
			SELECT `group_type`
			FROM `'._DB_PREFIX_.'attribute_group`
			WHERE `id_attribute_group` = (
				SELECT `id_attribute_group`
				FROM `'._DB_PREFIX_.'attribute`
				WHERE `id_attribute` = '.(int)$this->id.')
			AND group_type = \'color\''))
			return false;

		return Db::getInstance()->numRows();
	}

	/**
	 * Get minimal quantity for product with attributes quantity
	 *
	 * @acces public static
	 * @param integer $id_product_attribute
	 * @return mixed Minimal Quantity or false
	 */
	public static function getAttributeMinimalQty($id_product_attribute)
	{
		$minimal_quantity = Db::getInstance()->getValue('
			SELECT `minimal_quantity`
			FROM `'._DB_PREFIX_.'product_attribute_shop` pas
			WHERE `id_shop` = '.(int)Context::getContext()->shop->id.'
			AND `id_product_attribute` = '.(int)$id_product_attribute
		);

		if ($minimal_quantity > 1)
			return (int)$minimal_quantity;

		return false;
	}

	/**
	 * Move an attribute inside its group
	 * @param boolean $way Up (1)  or Down (0)
	 * @param integer $position
	 * @return boolean Update result
	 */
	public function updatePosition($way, $position)
	{
		if (!$id_attribute_group = (int)Tools::getValue('id_attribute_group'))
			$id_attribute_group = (int)$this->id_attribute_group;

		$sql = '
			SELECT a.`id_attribute`, a.`position`, a.`id_attribute_group`
			FROM `'._DB_PREFIX_.'attribute` a
			WHERE a.`id_attribute_group` = '.(int)$id_attribute_group.'
			ORDER BY a.`position` ASC';

		if (!$res = Db::getInstance()->executeS($sql))
			return false;

		foreach ($res as $attribute)
			if ((int)$attribute['id_attribute'] == (int)$this->id)
				$moved_attribute = $attribute;

		if (!isset($moved_attribute) || !isset($position))
			return false;

		// < and > statements rather than BETWEEN operator
		// since BETWEEN is treated differently according to databases

		$res1 = Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'attribute`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way
				? '> '.(int)$moved_attribute['position'].' AND `position` <= '.(int)$position
				: '< '.(int)$moved_attribute['position'].' AND `position` >= '.(int)$position).'
			AND `id_attribute_group`='.(int)$moved_attribute['id_attribute_group']
		);

		$res2 = Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'attribute`
			SET `position` = '.(int)$position.'
			WHERE `id_attribute` = '.(int)$moved_attribute['id_attribute'].'
			AND `id_attribute_group`='.(int)$moved_attribute['id_attribute_group']
		);

		return ($res1 && $res2);
	}

	/**
	 * Reorder attribute position in group $id_attribute_group.
	 * Call it after deleting an attribute from a group.
	 *
	 * @param int $id_attribute_group
	 * @param bool $use_last_attribute
	 * @return bool $return
	 */
	public function cleanPositions($id_attribute_group, $use_last_attribute = true)
	{
		$return = true;

		$sql = '
			SELECT `id_attribute`
			FROM `'._DB_PREFIX_.'attribute`
			WHERE `id_attribute_group` = '.(int)$id_attribute_group;

		// when delete, you must use $use_last_attribute
		if ($use_last_attribute)
			$sql .= ' AND `id_attribute` != '.(int)$this->id;

		$sql .= ' ORDER BY `position`';

		$result = Db::getInstance()->executeS($sql);

		$i = 0;
		foreach ($result as $value)
			$return = Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'attribute`
				SET `position` = '.(int)$i++.'
				WHERE `id_attribute_group` = '.(int)$id_attribute_group.'
				AND `id_attribute` = '.(int)$value['id_attribute']
			);

		return $return;
	}

	/**
	 * getHigherPosition
	 *
	 * Get the higher attribute position from a group attribute
	 *
	 * @param integer $id_attribute_group
	 * @return integer $position
	 */
	public static function getHigherPosition($id_attribute_group)
	{
		$sql = 'SELECT MAX(`position`)
				FROM `'._DB_PREFIX_.'attribute`
				WHERE id_attribute_group = '.(int)$id_attribute_group;

		$position = DB::getInstance()->getValue($sql);

		return (is_numeric($position)) ? $position : -1;
	}
}
