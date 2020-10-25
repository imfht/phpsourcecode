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

class Feeder extends Module
{
	private $_postErrors = array();
	
	public function __construct()
	{
		$this->name = 'feeder';
		$this->tab = 'front_office_features';
		$this->version = 0.2;
		$this->author = 'MileBiz';
		$this->need_instance = 0;
		
		$this->_directory = dirname(__FILE__).'/../../';
		parent::__construct();
		
		$this->displayName = $this->l('RSS products feed');
		$this->description = $this->l('Generate a RSS products feed');
	}
	
	function install()
	{
		if (!parent::install())
			return false;
		if (!$this->registerHook('header'))
			return false;
		return true;
	}
	
	function hookHeader($params)
	{
		$id_category = (int)(Tools::getValue('id_category'));
		if (!$id_category)
		{
			if (isset($_SERVER['HTTP_REFERER']) && preg_match('!^(.*)\/([0-9]+)\-(.*[^\.])|(.*)id_category=([0-9]+)(.*)$!', $_SERVER['HTTP_REFERER'], $regs) && !strstr($_SERVER['HTTP_REFERER'], '.html'))
			{
				if (isset($regs[2]) && is_numeric($regs[2]))
					$id_category = (int)($regs[2]);
				elseif (isset($regs[5]) && is_numeric($regs[5]))
					$id_category = (int)$regs[5];
			}
			elseif ($id_product = (int)Tools::getValue('id_product'))
			{
				$product = new Product($id_product);
				$id_category = $product->id_category_default;
			}
		}

		$orderBy = Tools::getProductsOrder('by', Tools::getValue('orderby'));
		$orderWay = Tools::getProductsOrder('way', Tools::getValue('orderway'));
		$this->smarty->assign(array(
			'feedUrl' => Tools::getShopDomain(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/rss.php?id_category='.$id_category.'&amp;orderby='.$orderBy.'&amp;orderway='.$orderWay,
		));
		return $this->display(__FILE__, 'feederHeader.tpl');
	}
}
