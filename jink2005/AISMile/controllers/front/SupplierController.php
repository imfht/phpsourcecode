<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class SupplierControllerCore extends FrontController
{
	public $php_self = 'supplier';

	protected $supplier;

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_THEME_CSS_DIR_.'product_list.css');

		if (Configuration::get('PS_COMPARATOR_MAX_ITEM'))
			$this->addJS(_THEME_JS_DIR_.'products-comparison.js');
	}

	public function canonicalRedirection($canonicalURL = '')
	{
		if (Validate::isLoadedObject($this->supplier))
			parent::canonicalRedirection($this->context->link->getSupplierLink($this->supplier));
	}

	/**
	 * Initialize supplier controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		parent::init();

		if ($id_supplier = (int)Tools::getValue('id_supplier'))
		{
			$this->supplier = new Supplier($id_supplier, $this->context->language->id);

			if (!Validate::isLoadedObject($this->supplier) || !$this->supplier->active)
			{
				header('HTTP/1.1 404 Not Found');
				header('Status: 404 Not Found');
				$this->errors[] = Tools::displayError('Supplier does not exist.');
			}
			else
				$this->canonicalRedirection();
		}
	}

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		if (Validate::isLoadedObject($this->supplier) && $this->supplier->active && $this->supplier->isAssociatedToShop())
		{
			$this->productSort(); // productSort must be called before assignOne
			$this->assignOne();
			$this->setTemplate(_PS_THEME_DIR_.'supplier.tpl');
		}
		else
		{
			$this->assignAll();
			$this->setTemplate(_PS_THEME_DIR_.'supplier-list.tpl');
		}
	}

	/**
	 * Assign template vars if displaying one supplier
	 */
	protected function assignOne()
	{
		$nbProducts = $this->supplier->getProducts($this->supplier->id, null, null, null, $this->orderBy, $this->orderWay, true);
		$this->pagination((int)$nbProducts);
		$this->context->smarty->assign(array(
			'nb_products' => $nbProducts,
			'products' => $this->supplier->getProducts($this->supplier->id, $this->context->cookie->id_lang, (int)$this->p, (int)$this->n, $this->orderBy, $this->orderWay),
			'path' => ($this->supplier->active ? Tools::safeOutput($this->supplier->name) : ''),
			'supplier' => $this->supplier,
			'comparator_max_item' => Configuration::get('PS_COMPARATOR_MAX_ITEM')
		));
	}

	/**
	 * Assign template vars if displaying the supplier list
	 */
	protected function assignAll()
	{
		if (Configuration::get('PS_DISPLAY_SUPPLIERS'))
		{
			$result = Supplier::getSuppliers(true, $this->context->language->id, true);
			$nbProducts = count($result);
			$this->pagination($nbProducts);

			$suppliers = Supplier::getSuppliers(true, $this->context->language->id, true, $this->p, $this->n);
			foreach ($suppliers as &$row)
				$row['image'] = (!file_exists(_PS_SUPP_IMG_DIR_.'/'.$row['id_supplier'].'-medium_default.jpg')) ? $this->context->language->iso_code.'-default' : $row['id_supplier'];

			$this->context->smarty->assign(array(
				'pages_nb' => ceil($nbProducts / (int)$this->n),
				'nbSuppliers' => $nbProducts,
				'mediumSize' => Image::getSize('medium_default'),
				'suppliers_list' => $suppliers,
				'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
			));
		}
		else
			$this->context->smarty->assign('nbSuppliers', 0);
	}
}
