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
	
class BlockBestSellers extends Module
{
	private $_html = '';
	private $_postErrors = array();

	public function __construct()
	{
		$this->name = 'blockbestsellers';
		$this->tab = 'front_office_features';
		$this->version = '1.1';
		$this->author = 'MileBiz';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Top seller block');
		$this->description = $this->l('Add a block displaying the shop\'s top sellers.');
	}

	/**
	 * @see ModuleCore::install()
	 */
	public function install()
	{
		if (!parent::install() ||
				!$this->registerHook('rightColumn') ||
				!$this->registerHook('header') ||
				!$this->registerHook('updateOrderStatus') ||
				!ProductSale::fillProductSales())
			return false;
		return true;
	}

	/**
	 * Called in administration -> module -> configure
	 */
	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitBestSellers'))
		{
			Configuration::updateValue('PS_BLOCK_BESTSELLERS_DISPLAY', (int)(Tools::getValue('always_display')));
			$output .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		return '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset>
				<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Always display block').'</label>
				<div class="margin-form">
					<input type="radio" name="always_display" id="display_on" value="1" '.(Tools::getValue('always_display', Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="display_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="always_display" id="display_off" value="0" '.(!Tools::getValue('always_display', Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="display_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p class="clear">'.$this->l('Show the block even if no product is available.').'</p>
				</div>
				<center><input type="submit" name="submitBestSellers" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
	}
	
	public function hookRightColumn($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;

		$currency = new Currency($params['cookie']->id_currency);
		$bestsellers = ProductSale::getBestSalesLight((int)($params['cookie']->id_lang), 0, 5);
		
		if (!$bestsellers && !Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY'))
			return;
		$best_sellers = array();

		if ($bestsellers)
			foreach ($bestsellers as $bestseller)
			{
				$bestseller['price'] = Tools::displayPrice(Product::getPriceStatic((int)($bestseller['id_product'])), $currency);
				$best_sellers[] = $bestseller;
			}

		$this->smarty->assign(array(
			'best_sellers' => $best_sellers,
			'mediumSize' => Image::getSize('medium_default'),
			'smallSize' => Image::getSize('small_default')
		));
		return $this->display(__FILE__, 'blockbestsellers.tpl');
	}
	
	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookHeader($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;
		$this->context->controller->addCSS(($this->_path).'blockbestsellers.css', 'all');
	}
	
	public function hookHome($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;

		$currency = new Currency($params['cookie']->id_currency);
		$bestsellers = ProductSale::getBestSalesLight((int)$params['cookie']->id_lang, 0, 4);
		if (!$bestsellers && !Configuration::get('PS_BLOCK_BESTSELLERS_DISPLAY'))
			return;
		$best_sellers = array();
		
		if ($bestsellers)
			foreach ($bestsellers as $bestseller)
			{
				$bestseller['price'] = Tools::displayPrice(Product::getPriceStatic((int)($bestseller['id_product'])), $currency);
				$best_sellers[] = $bestseller;
}

		$this->smarty->assign(array(
			'best_sellers' => $best_sellers,
			'homeSize' => Image::getSize('home_default')));
		return $this->display(__FILE__, 'blockbestsellers-home.tpl');
	}
}


