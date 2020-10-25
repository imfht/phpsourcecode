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

class StatsBestVouchers extends ModuleGrid
{
	private $_html;
	private $_query;
	private $_columns;
	private $_defaultSortColumn;
	private $_defaultSortDirection;
	private $_emptyMessage;
	private $_pagingMessage;

	public function __construct()
	{
		$this->name = 'statsbestvouchers';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'MileBiz';
		$this->need_instance = 0;
		
		parent::__construct();
		
		$this->_defaultSortColumn = 'ca';
		$this->_defaultSortDirection = 'DESC';
		$this->_emptyMessage = $this->l('Empty recordset returned');
		$this->_pagingMessage = sprintf($this->l('Displaying %1$s of %2$s'), '{0} - {1}', '{2}');

		$this->_columns = array(
			array(
				'id' => 'name',
				'header' => $this->l('Name'),
				'dataIndex' => 'name',
				'align' => 'left',
				'width' => 300
			),
			array(
				'id' => 'ca',
				'header' => $this->l('Sales'),
				'dataIndex' => 'ca',
				'width' => 30,
				'align' => 'right'
			),
			array(
				'id' => 'total',
				'header' => $this->l('Total used'),
				'dataIndex' => 'total',
				'width' => 30,
				'align' => 'right'
			)
		);

		$this->displayName = $this->l('Best vouchers');
		$this->description = $this->l('A list of the best vouchers');
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
			<br /><a class="button export-csv" href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&export=1"><span>'.$this->l('CSV Export').'</span></a>
		</div>';
		return $this->_html;
	}

	public function getData()
	{
		$this->_query = 'SELECT SQL_CALC_FOUND_ROWS ocr.code, COUNT(ocr.id_cart_rule) as total, SUM(o.total_paid_real) / o.conversion_rate as ca
				FROM '._DB_PREFIX_.'order_cart_rule ocr
				LEFT JOIN '._DB_PREFIX_.'orders o ON o.id_order = ocr.id_order
				WHERE o.valid = 1
					'.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
					AND o.invoice_date BETWEEN '.$this->getDate().'
				GROUP BY ocr.id_cart_rule';
		if (Validate::IsName($this->_sort))
		{
			$this->_query .= ' ORDER BY `'.$this->_sort.'`';
			if (isset($this->_direction) && (strtoupper($this->_direction) == 'ASC' ||聽strtoupper($this->_direction) == 'DESC'))
				$this->_query .= ' '.pSQL($this->_direction);
		}
		if (($this->_start === 0 || Validate::IsUnsignedInt($this->_start)) && Validate::IsUnsignedInt($this->_limit))
			$this->_query .= ' LIMIT '.$this->_start.', '.($this->_limit);
		$this->_values = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($this->_query);
		$this->_totalCount = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT FOUND_ROWS()');
	}
}
