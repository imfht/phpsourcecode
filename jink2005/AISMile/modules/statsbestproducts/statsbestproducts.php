<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

if (!defined('_MB_VERSION_'))
	exit;

class StatsBestProducts extends ModuleGrid
{
	private $_html = null;
	private $_query =  null;
	private $_columns = null;
	private $_defaultSortColumn = null;
	private $_defaultSortDirection = null;
	private $_emptyMessage = null;
	private $_pagingMessage = null;

	public function __construct()
	{
		$this->name = 'statsbestproducts';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'MileBiz';
		$this->need_instance = 0;

		parent::__construct();

		$this->_defaultSortColumn = 'totalPriceSold';
		$this->_defaultSortDirection = 'DESC';
		$this->_emptyMessage = $this->l('Empty recordset returned');
		$this->_pagingMessage = sprintf($this->l('Displaying %1$s of %2$s'), '{0} - {1}', '{2}');

		$this->_columns = array(
			array(
				'id' => 'reference',
				'header' => $this->l('Ref.'),
				'dataIndex' => 'reference',
				'align' => 'left',
				'width' => 50
			),
			array(
				'id' => 'name',
				'header' => $this->l('Name'),
				'dataIndex' => 'name',
				'align' => 'left',
				'width' => 100
			),
			array(
				'id' => 'totalQuantitySold',
				'header' => $this->l('Quantity sold'),
				'dataIndex' => 'totalQuantitySold',
				'width' => 50,
				'align' => 'right'
			),
			array(
				'id' => 'avgPriceSold',
				'header' => $this->l('Price sold'),
				'dataIndex' => 'avgPriceSold',
				'width' => 50,
				'align' => 'right'
			),
			array(
				'id' => 'totalPriceSold',
				'header' => $this->l('Sales'),
				'dataIndex' => 'totalPriceSold',
				'width' => 50,
				'align' => 'right'
			),
			array(
				'id' => 'averageQuantitySold',
				'header' => $this->l('Quantity sold/ day'),
				'dataIndex' => 'averageQuantitySold',
				'width' => 60,
				'align' => 'right'
			),
			array(
				'id' => 'totalPageViewed',
				'header' => $this->l('Page viewed'),
				'dataIndex' => 'totalPageViewed',
				'width' => 60,
				'align' => 'right'
			),
			array(
				'id' => 'quantity',
				'header' => $this->l('Available quantity for sale'),
				'dataIndex' => 'quantity',
				'width' => 150,
				'align' => 'right'
			)
		);
		
		$this->displayName = $this->l('Best products');
		$this->description = $this->l('A list of the best products');
	}

	public function install()
	{
		return (parent::install() && $this->registerHook('AdminStatsModules'));
	}

	public function hookAdminStatsModules($params)
	{
		$engineParams = array(
			'id' => 'id_product',
			'title' => $this->displayName,
			'columns' => $this->_columns,
			'defaultSortColumn' => $this->_defaultSortColumn,
			'defaultSortDirection' => $this->_defaultSortDirection,
			'emptyMessage' => $this->_emptyMessage,
			'pagingMessage' => $this->_pagingMessage
		);

		if (Tools::getValue('export'))
			$this->csvExport($engineParams);

		$this->_html = '
		<div class="blocStats"><h2 class="icon-'.$this->name.'"><span></span>'.$this->displayName.'</h2>
			'.$this->engine($engineParams).'
			<p><a class="button export-csv" href="'.htmlentities($_SERVER['REQUEST_URI']).'&export=1"><span>'.$this->l('CSV Export').'</span></a></p>
		</fieldset>';
		return $this->_html;
	}

	public function getData()
	{
		$dateBetween = $this->getDate();
		$arrayDateBetween = explode(' AND ', $dateBetween);

		$this->_query = 'SELECT SQL_CALC_FOUND_ROWS p.reference, p.id_product, pl.name,
				ROUND(AVG(od.product_price / o.conversion_rate), 2) as avgPriceSold,
				IFNULL(stock.quantity, 0) as quantity,
				IFNULL(SUM(od.product_quantity), 0) AS totalQuantitySold,
				ROUND(IFNULL(IFNULL(SUM(od.product_quantity), 0) / (1 + LEAST(TO_DAYS('.$arrayDateBetween[1].'), TO_DAYS(NOW())) - GREATEST(TO_DAYS('.$arrayDateBetween[0].'), TO_DAYS(product_shop.date_add))), 0), 2) as averageQuantitySold,
				ROUND(IFNULL(SUM((od.product_price * od.product_quantity) / o.conversion_rate), 0), 2) AS totalPriceSold,
				(
					SELECT IFNULL(SUM(pv.counter), 0)
					FROM '._DB_PREFIX_.'page pa
					LEFT JOIN '._DB_PREFIX_.'page_viewed pv ON pa.id_page = pv.id_page
					LEFT JOIN '._DB_PREFIX_.'date_range dr ON pv.id_date_range = dr.id_date_range
					WHERE pa.id_object = p.id_product AND pa.id_page_type = ('.(int)Page::getPageTypeByName('product').')
					AND dr.time_start BETWEEN '.$dateBetween.'
					AND dr.time_end BETWEEN '.$dateBetween.'
				) AS totalPageViewed
				FROM '._DB_PREFIX_.'product p
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (p.id_product = pl.id_product AND pl.id_lang = '.(int)$this->getLang().Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN '._DB_PREFIX_.'order_detail od ON od.product_id = p.id_product
				LEFT JOIN '._DB_PREFIX_.'orders o ON od.id_order = o.id_order
				'.Product::sqlStock('p', 0).'
				WHERE product_shop.active = 1
					AND o.valid = 1
					AND o.invoice_date BETWEEN '.$dateBetween.'
				GROUP BY od.product_id';

		if (Validate::IsName($this->_sort))
		{
			$this->_query .= ' ORDER BY `'.$this->_sort.'`';
			if (isset($this->_direction) && Validate::isSortDirection($this->_direction))
				$this->_query .= ' '.$this->_direction;
		}

		if (($this->_start === 0 || Validate::IsUnsignedInt($this->_start)) && Validate::IsUnsignedInt($this->_limit))
			$this->_query .= ' LIMIT '.$this->_start.', '.($this->_limit);
		$this->_values = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query);
		$this->_totalCount = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT FOUND_ROWS()');
	}
}
