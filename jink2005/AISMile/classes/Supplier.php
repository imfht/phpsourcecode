<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class SupplierCore extends ObjectModel
{
	public $id;

	/** @var integer supplier ID */
	public $id_supplier;

	/** @var string Name */
	public $name;

	/** @var string A short description for the discount */
	public $description;

	/** @var string Object creation date */
	public $date_add;

	/** @var string Object last modification date */
	public $date_upd;

	/** @var string Friendly URL */
	public $link_rewrite;

	/** @var string Meta title */
	public $meta_title;

	/** @var string Meta keywords */
	public $meta_keywords;

	/** @var string Meta description */
	public $meta_description;

	/** @var boolean active */
	public $active;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'supplier',
		'primary' => 'id_supplier',
		'multilang' => true,
		'fields' => array(
			'name' => 				array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'required' => true, 'size' => 64),
			'active' => 			array('type' => self::TYPE_BOOL),
			'date_add' => 			array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' => 			array('type' => self::TYPE_DATE, 'validate' => 'isDate'),

			// Lang fields
			'description' => 		array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'),
			'meta_title' => 		array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 128),
			'meta_description' => 	array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
			'meta_keywords' => 		array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255),
		),
	);

	protected	$webserviceParameters = array(
		'fields' => array(
			'link_rewrite' => array('sqlId' => 'link_rewrite'),
		),
	);

	public function __construct($id = null, $id_lang = null)
	{
		parent::__construct($id, $id_lang);

		$this->link_rewrite = $this->getLink();
		$this->image_dir = _PS_SUPP_IMG_DIR_;
	}

	public function getLink()
	{
		return Tools::link_rewrite($this->name, false);
	}

	/**
	  * Return suppliers
	  *
	  * @return array Suppliers
	  */
	public static function getSuppliers($get_nb_products = false, $id_lang = 0, $active = true, $p = false, $n = false, $all_groups = false)
	{
		if (!$id_lang)
			$id_lang = Configuration::get('PS_LANG_DEFAULT');

		$query = new DbQuery();
		$query->select('s.*, sl.`description`');
		$query->from('supplier', 's');
		$query->leftJoin('supplier_lang', 'sl', 's.`id_supplier` = sl.`id_supplier` AND sl.`id_lang` = '.(int)$id_lang);
		$query->join(Shop::addSqlAssociation('supplier', 's'));
		if ($active)
			$query->where('s.`active` = 1');
		$query->orderBy(' s.`name` ASC');
		$query->limit($n, ($p - 1) * $n);
		$query->groupBy('s.id_supplier');

		$suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
		if ($suppliers === false)
			return false;
		if ($get_nb_products)
		{
			$sql_groups = '';
			if (!$all_groups)
			{
				$groups = FrontController::getCurrentCustomerGroups();
				$sql_groups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
			}
			foreach ($suppliers as $key => $supplier)
			{
				$sql = '
					SELECT DISTINCT(ps.`id_product`)
					FROM `'._DB_PREFIX_.'product_supplier` ps
					JOIN `'._DB_PREFIX_.'product` p ON (ps.`id_product`= p.`id_product`)
					'.Shop::addSqlAssociation('product', 'p').'
					WHERE ps.`id_supplier` = '.(int)$supplier['id_supplier'].'
					AND ps.id_product_attribute = 0'.
					($active ? ' AND product_shop.`active` = 1' : '').
					($all_groups ? '' :'
					AND ps.`id_product` IN (
						SELECT cp.`id_product`
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE cg.`id_group` '.$sql_groups.'
					)');
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
				$suppliers[$key]['nb_products'] = count($result);
			}
		}

		$nb_suppliers = count($suppliers);
		$rewrite_settings = (int)Configuration::get('PS_REWRITING_SETTINGS');
		for ($i = 0; $i < $nb_suppliers; $i++)
			if ($rewrite_settings)
				$suppliers[$i]['link_rewrite'] = Tools::link_rewrite($suppliers[$i]['name'], false);
			else
				$suppliers[$i]['link_rewrite'] = 0;
		return $suppliers;
	}

	/**
	  * Return name from id
	  *
	  * @param integer $id_supplier Supplier ID
	  * @return string name
	  */
	static protected $cache_name = array();
	public static function getNameById($id_supplier)
	{
		if (!isset(self::$cache_name[$id_supplier]))
			self::$cache_name[$id_supplier] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `name` FROM `'._DB_PREFIX_.'supplier` WHERE `id_supplier` = '.(int)$id_supplier);
		return self::$cache_name[$id_supplier];
	}

	public static function getIdByName($name)
	{
		$result = Db::getInstance()->getRow('
		SELECT `id_supplier`
		FROM `'._DB_PREFIX_.'supplier`
		WHERE `name` = \''.pSQL($name).'\'');

		if (isset($result['id_supplier']))
			return (int)$result['id_supplier'];

		return false;
	}

	public static function getProducts($id_supplier, $id_lang, $p, $n,
		$order_by = null, $order_way = null, $get_total = false, $active = true, $active_category = true)
	{
		$context = Context::getContext();
		$front = true;
		if (!in_array($context->controller->controller_type, array('front', 'modulefront')))
			$front = false;

		if ($p < 1) $p = 1;
	 	if (empty($order_by) || $order_by == 'position') $order_by = 'name';
	 	if (empty($order_way)) $order_way = 'ASC';

		if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way))
			die (Tools::displayError());

		$groups = FrontController::getCurrentCustomerGroups();
		$sql_groups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');

		/* Return only the number of products */
		if ($get_total)
		{
			$sql = '
				SELECT DISTINCT(ps.`id_product`)
				FROM `'._DB_PREFIX_.'product_supplier` ps
				JOIN `'._DB_PREFIX_.'product` p ON (ps.`id_product`= p.`id_product`)
				'.Shop::addSqlAssociation('product', 'p').'
				WHERE ps.`id_supplier` = '.(int)$id_supplier.'
				AND ps.id_product_attribute = 0'.
				($active ? ' AND product_shop.`active` = 1' : '').'
				'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
				AND p.`id_product` IN (
					SELECT cp.`id_product`
					FROM `'._DB_PREFIX_.'category_group` cg
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)'.
					($active_category ? ' INNER JOIN `'._DB_PREFIX_.'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1' : '').'
					WHERE cg.`id_group` '.$sql_groups.'
				)';
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
			return (int)count($result);
		}

		$nb_days_new_product = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;

		if (strpos('.', $order_by) > 0)
		{
			$order_by = explode('.', $order_by);
			$order_by = pSQL($order_by[0]).'.`'.pSQL($order_by[1]).'`';
		}
		$alias = '';
		if ($order_by == 'price')
			$alias = 'product_shop.';
		elseif ($order_by == 'id_product')
			$alias = 'p.';
		$sql = 'SELECT p.*, product_shop.*, stock.out_of_stock,
					IFNULL(stock.quantity, 0) as quantity,
					pl.`description`,
					pl.`description_short`,
					pl.`link_rewrite`,
					pl.`meta_description`,
					pl.`meta_keywords`,
					pl.`meta_title`,
					pl.`name`,
					image_shop.`id_image`,
					il.`legend`,
					s.`name` AS supplier_name,
					tl.`name` AS tax_name,
					t.`rate`,
					DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.($nb_days_new_product).' DAY)) > 0 AS new,
					(p.`price` * ((100 + (t.`rate`))/100)) AS orderprice,
					m.`name` AS manufacturer_name
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				JOIN `'._DB_PREFIX_.'product_supplier` ps ON (ps.id_product = p.id_product
					AND ps.id_product_attribute = 0)
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`
					AND tr.`id_country` = '.(int)Context::getContext()->country->id.'
					AND tr.`id_state` = 0
					AND tr.`zipcode_from` = 0)
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (t.`id_tax` = tl.`id_tax`
					AND tl.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'supplier` s ON s.`id_supplier` = p.`id_supplier`
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				'.Product::sqlStock('p').'
				WHERE ps.`id_supplier` = '.(int)$id_supplier.
					($active ? ' AND product_shop.`active` = 1' : '').'
					'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
					AND p.`id_product` IN (
						SELECT cp.`id_product`
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)'.
						($active_category ? ' INNER JOIN `'._DB_PREFIX_.'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1' : '').'
						WHERE cg.`id_group` '.$sql_groups.'
					)
					AND ((image_shop.id_image IS NOT NULL OR i.id_image IS NULL) OR (image_shop.id_image IS NULL AND i.cover=1))
				ORDER BY '.$alias.pSQL($order_by).' '.pSQL($order_way).'
				LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

		if (!$result)
			return false;

		if ($order_by == 'price')
			Tools::orderbyPrice($result, $order_way);

		return Product::getProductsProperties($id_lang, $result);
	}

	public function getProductsLite($id_lang)
	{
		$context = Context::getContext();
		$front = true;
		if (!in_array($context->controller->controller_type, array('front', 'modulefront')))
			$front = false;

		$sql = '
			SELECT p.`id_product`,
				   pl.`name`
			FROM `'._DB_PREFIX_.'product` p
			'.Shop::addSqlAssociation('product', 'p').'
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
				p.`id_product` = pl.`id_product`
				AND pl.`id_lang` = '.(int)$id_lang.'
			)
			INNER JOIN `'._DB_PREFIX_.'product_supplier` ps ON (
				ps.`id_product` = p.`id_product`
				AND ps.`id_supplier` = '.(int)$this->id.'
			)
			'.($front ? ' WHERE product_shop.`visibility` IN ("both", "catalog")' : '').'
			GROUP BY p.`id_product`';

		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		return $res;
	}

	/*
	* Tells if a supplier exists
	*
	* @param $id_supplier Supplier id
	* @return boolean
	*/
	public static function supplierExists($id_supplier)
	{
		$query = new DbQuery();
		$query->select('id_supplier');
		$query->from('supplier');
		$query->where('id_supplier = '.(int)$id_supplier);
		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

		return ($res > 0);
	}

	/**
	 * @see ObjectModel::delete()
	 */
	public function delete()
	{
		if (parent::delete())
		{
			CartRule::cleanProductRuleIntegrity('suppliers', $this->id);
			return $this->deleteImage();
		}
	}

	/**
	 * Gets product informations
	 *
	 * @since 1.5.0
	 * @param int $id_supplier
	 * @param int $id_product
	 * @param int $id_product_attribute
	 * @return array
	 */
	public static function getProductInformationsBySupplier($id_supplier, $id_product, $id_product_attribute = 0)
	{
		$query = new DbQuery();
		$query->select('product_supplier_reference, product_supplier_price_te, id_currency');
		$query->from('product_supplier');
		$query->where('id_supplier = '.(int)$id_supplier);
		$query->where('id_product = '.(int)$id_product);
		$query->where('id_product_attribute = '.(int)$id_product_attribute);
		$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		if (count($res))
			return $res[0];
	}
}

