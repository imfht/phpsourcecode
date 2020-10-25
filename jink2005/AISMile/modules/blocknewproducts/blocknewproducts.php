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

class BlockNewProducts extends Module
{
	public function __construct()
	{
		$this->name = 'blocknewproducts';
		$this->tab = 'front_office_features';
		$this->version = 0.9;
		$this->author = 'MileBiz';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('New products block');
		$this->description = $this->l('Displays a block featuring newly added products.');
	}

	public function install()
	{
			if (parent::install() == false || $this->registerHook('rightColumn') == false || $this->registerHook('header') == false || Configuration::updateValue('NEW_PRODUCTS_NBR', 5) == false)
					return false;
			return true;
	}

	public function getContent()
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitBlockNewProducts'))
		{
			if (!($productNbr = Tools::getValue('productNbr')) || empty($productNbr))
				$output .= '<div class="alert error">'.$this->l('Please fill in the "products displayed" field.').'</div>';
			elseif ((int)($productNbr) == 0)
				$output .= '<div class="alert error">'.$this->l('Invalid number.').'</div>';
			else
			{
				Configuration::updateValue('PS_BLOCK_NEWPRODUCTS_DISPLAY', (int)(Tools::getValue('always_display')));
				Configuration::updateValue('NEW_PRODUCTS_NBR', (int)($productNbr));
				$output .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
			}
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		$output = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
		<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<label>'.$this->l('Products displayed').'</label>
					<div class="margin-form">
						<input type="text" name="productNbr" value="'.(int)(Configuration::get('NEW_PRODUCTS_NBR')).'" />
						<p class="clear">'.$this->l('Set the number of products to be displayed in this block').'</p>
					</div>
					<label>'.$this->l('Always display block').'</label>
					<div class="margin-form">
						<input type="radio" name="always_display" id="display_on" value="1" '.(Tools::getValue('always_display', Configuration::get('PS_BLOCK_NEWPRODUCTS_DISPLAY')) ? 'checked="checked" ' : '').'/>
						<label class="t" for="display_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
						<input type="radio" name="always_display" id="display_off" value="0" '.(!Tools::getValue('always_display', Configuration::get('PS_BLOCK_NEWPRODUCTS_DISPLAY')) ? 'checked="checked" ' : '').'/>
						<label class="t" for="display_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
						<p class="clear">'.$this->l('Show the block even if no products are available.').'</p>
					</div>
					<center><input type="submit" name="submitBlockNewProducts" value="'.$this->l('Save').'" class="button" /></center>
				</fieldset>
			</form>';
		return $output;
	}

	public function hookRightColumn($params)
	{
		$newProducts = Product::getNewProducts((int)($params['cookie']->id_lang), 0, (int)(Configuration::get('NEW_PRODUCTS_NBR')));
		if (!$newProducts && !Configuration::get('PS_BLOCK_NEWPRODUCTS_DISPLAY'))
			return;

		$this->smarty->assign(array(
			'new_products' => $newProducts,
			'mediumSize' => Image::getSize('medium_default'),
		));

		return $this->display(__FILE__, 'blocknewproducts.tpl');
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blocknewproducts.css', 'all');
	}

}


