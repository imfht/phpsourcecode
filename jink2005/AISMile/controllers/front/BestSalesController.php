<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class BestSalesControllerCore extends FrontController
{
	public $php_self = 'best-sales';

	public function initContent()
	{
		parent::initContent();

		$this->productSort();
		$nbProducts = (int)ProductSale::getNbSales();
		$this->pagination($nbProducts);

		$this->context->smarty->assign(array(
			'products' => ProductSale::getBestSales($this->context->language->id, $this->p - 1, $this->n, $this->orderBy, $this->orderWay),
			'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
			'nbProducts' => $nbProducts,
			'homeSize' => Image::getSize('home_default'),
			'comparator_max_item' => Configuration::get('PS_COMPARATOR_MAX_ITEM')
		));

		$this->setTemplate(_PS_THEME_DIR_.'best-sales.tpl');
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_THEME_CSS_DIR_.'product_list.css');

		if (Configuration::get('PS_COMPARATOR_MAX_ITEM'))
			$this->addJS(_THEME_JS_DIR_.'products-comparison.js');
	}
}

