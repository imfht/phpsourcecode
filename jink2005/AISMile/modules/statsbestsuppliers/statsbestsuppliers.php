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

class StatsBestSuppliers extends ModuleGrid
{
	private $_html = null;
	private $_query = null;
	private $_columns = null;
	private $_defaultSortColumn = null;
	private $_defaultSortDirection = null;
	private $_emptyMessage = null;
	private $_pagingMessage = null;

	public function __construct()
	{
		$this->name = 'statsbestsuppliers';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'MileBiz';
		$this->need_instance = 0;

		parent::__construct();

		$this->_defaultSortColumn = 'sales';
		$this->_defaultSortDirection = 'DESC';
		$this->_emptyMessage = $this->l('Empty recordset returned');
		$this->_pagingMessage = sprintf($this->l('Displaying %1$s of %2$s'), '{0} - {1}', '{2}');

		$this->_columns = array(
			array(
				'id' => 'name',
				'header' => $this->l('Name'),
				'dataIndex' => 'name',
				'align' => 'left',
				'width' => 200
			),
			array(
				'id' => 'quantity',
				'header' => $this->l('Quantity sold'),
				'dataIndex' => 'quantity',
				'width' => 60,
				'align' => 'right'
			),
			array(
				'id' => 'sales',
				'header' => $this->l('Total paid'),
				'dataIndex' => 'sales',
				'width' => 60,
				'align' => 'right'
			)
		);

		$this->displayName = $this->l('Best suppliers');
		$this->description = $this->l('A list of the best suppliers');
	}

	public function install()
	{
		return (parent::install() && $this->registerHook('AdminStatsModules'));
	}

	public function hookAdminStatsModules($params)
	{
		$engineParams = array(
			'id' => 'id_category',
			'title' => $this->displayName,
			'columns' => $this->_columns,
			'defaultSortColumn' => $this->_defaultSortColumn,
			'defaultSortDirection' => $this->_defaultSortDirection,
			'emptyMessage' => $this->_emptyMessage,
			'pagingMessage' => $this->_pagingMessage
		);

		if (Tools::getValue('export') == 1)
				$this->csvExport($engineParams);
		$this->_html = '
		<div class="blocStats"><h2 class="icon-'.$this->name.'"><span></span>'.$this->displayName.'</h2>
			'.$this->engine($engineParams).'
			<p><a class="button export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1"><span>'.$this->l('CSV Export').'</span></a></p>
		</div>';
		return $this->_html;
	}

	/**
	 * @return int Get total of distinct suppliers
	 */
	public function getTotalCount()
	{
		$sql = 'SELECT COUNT(DISTINCT(s.id_supplier))
				FROM '._DB_PREFIX_.'order_detail od
				LEFT JOIN '._DB_PREFIX_.'product p ON p.id_product = od.product_id
				LEFT JOIN '._DB_PREFIX_.'orders o ON o.id_order = od.id_order
				LEFT JOIN '._DB_PREFIX_.'supplier s ON s.id_supplier = p.id_supplier
				WHERE o.invoice_date BETWEEN '.$this->getDate().'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					AND o.valid = 1
					AND s.id_supplier IS NOT NULL';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
	}

	public function getData()
	{
		$this->_totalCount = $this->getTotalCount();

		$this->_query = 'SELECT s.name, SUM(od.product_quantity) as quantity, ROUND(SUM(od.product_quantity * od.product_price) / o.conversion_rate, 2) as sales
				FROM '._DB_PREFIX_.'order_detail od
				LEFT JOIN '._DB_PREFIX_.'product p ON p.id_product = od.product_id
				LEFT JOIN '._DB_PREFIX_.'orders o ON o.id_order = od.id_order
				LEFT JOIN '._DB_PREFIX_.'supplier s ON s.id_supplier = p.id_supplier
				WHERE o.invoice_date BETWEEN '.$this->getDate().'
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					AND o.valid = 1
					AND s.id_supplier IS NOT NULL
				GROUP BY p.id_supplier';
		if (Validate::IsName($this->_sort))
		{
			$this->_query .= ' ORDER BY `'.$this->_sort.'`';
			if (isset($this->_direction) && Validate::isSortDirection($this->_direction))
				$this->_query .= ' '.$this->_direction;
		}

		if (($this->_start === 0 || Validate::IsUnsignedInt($this->_start)) && Validate::IsUnsignedInt($this->_limit))
			$this->_query .= ' LIMIT '.$this->_start.', '.($this->_limit);
		$this->_values = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query);
	}
}