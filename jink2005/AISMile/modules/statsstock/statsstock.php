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

class StatsStock extends Module
{
	private $html = '';

	public function __construct()
	{
		$this->name = 'statsstock';
		$this->tab = 'analytics_stats';
		$this->version = 1.0;
		$this->author = 'MileBiz';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Available quantities stats');
		$this->description = '';
	}

	public function install()
	{
		return parent::install() && $this->registerHook('AdminStatsModules');
	}

	public function hookAdminStatsModules()
	{
		if (Tools::isSubmit('submitCategory'))
			$this->context->cookie->statsstock_id_category = Tools::getValue('statsstock_id_category');

		$ru = AdminController::$currentIndex.'&module='.$this->name.'&token='.Tools::getValue('token');
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		$filter = ((int)$this->context->cookie->statsstock_id_category ? ' AND p.id_product IN (SELECT cp.id_product FROM '._DB_PREFIX_.'category_product cp WHERE cp.id_category = '.(int)$this->context->cookie->statsstock_id_category.')' : '');

		$sql = 'SELECT p.id_product, p.reference, pl.name,
				IFNULL((
					SELECT AVG(product_attribute_shop.wholesale_price)
					FROM '._DB_PREFIX_.'product_attribute pa
					'.Shop::addSqlAssociation('product_attribute', 'pa').'
					WHERE p.id_product = pa.id_product
					AND product_attribute_shop.wholesale_price != 0
				), product_shop.wholesale_price) as wholesale_price,
				IFNULL(stock.quantity, 0) as quantity
				FROM '._DB_PREFIX_.'product p
				'.Shop::addSqlAssociation('product', 'p').'
				INNER JOIN '._DB_PREFIX_.'product_lang pl
					ON (p.id_product = pl.id_product AND pl.id_lang = '.(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('pl').')
				'.Product::sqlStock('p', 0).'
				WHERE 1 = 1
				'.$filter;
		$products = Db::getInstance()->executeS($sql);

		foreach ($products as $key => $p)
		{
			$products[$key]['stockvalue'] = $p['wholesale_price'] * $p['quantity'];
		}

		$this->html .= '
		<script type="text/javascript">$(\'#calendar\').slideToggle();</script>
		<div class="blocStats"><h2 class="icon-'.$this->name.'"><span></span>'.$this->l('Available quantities for sale valuation').'</h2>
		<form action="'.$ru.'" method="post">
			<input type="hidden" name="submitCategory" value="1" />
			'.$this->l('Category').' : <select name="statsstock_id_category" onchange="this.form.submit();">
				<option value="0">-- '.$this->l('All').' --</option>';
		foreach (Category::getSimpleCategories($this->context->language->id) as $category)
			$this->html .= '<option value="'.(int)$category['id_category'].'" '.
				($this->context->cookie->statsstock_id_category == $category['id_category'] ? 'selected="selected"' : '').'>'.
				$category['name'].'
			</option>';
		$this->html .= '</select>
		</form><br />';

		if (!count($products))
			$this->html .= $this->l('Your catalog is empty.');
		else
		{
			$rollup = array('quantity' => 0, 'wholesale_price' => 0, 'stockvalue' => 0);
			$this->html .= '<table class="table" cellspacing="0" cellpadding="0">
			<tr>
				<th>'.$this->l('ID').'</th>
				<th>'.$this->l('Ref.').'</th>
				<th style="width:350px">'.$this->l('Item').'</th>
				<th>'.$this->l('Available quantity for sale').'</th>
				<th>'.$this->l('Price*').'</th>
				<th>'.$this->l('Value').'</th>
			</tr>';
			foreach ($products as $product)
			{
				$rollup['quantity'] += $product['quantity'];
				$rollup['wholesale_price'] += $product['wholesale_price'];
				$rollup['stockvalue'] += $product['stockvalue'];
				$this->html .= '<tr>
					<td>'.$product['id_product'].'</td>
					<td>'.$product['reference'].'</td>
					<td>'.$product['name'].'</td>
					<td>'.$product['quantity'].'</td>
					<td>'.Tools::displayPrice($product['wholesale_price'], $currency).'</td>
					<td>'.Tools::displayPrice($product['stockvalue'], $currency).'</td>
				</tr>';
			}
			$this->html .= '
				<tr>
					<th colspan="3"></th>
					<th>'.$this->l('Total quantities').'</th>
					<th>'.$this->l('Avg price').'</th>
					<th>'.$this->l('Total value').'</th>
				</tr>
				<tr>
					<td colspan="3"></td>
					<td>'.$rollup['quantity'].'</td>
					<td>'.Tools::displayPrice($rollup['wholesale_price'] / count($products), $currency).'</td>
					<td>'.Tools::displayPrice($rollup['stockvalue'], $currency).'</td>
				</tr>
			</table>
			<p>* '.$this->l('Correspond to the default wholesale price according to the default supplier for the product. An average price is used when the product has attributes.').'</p></div>';

			return $this->html;
		}
	}
}