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

class sendToAFriend extends Module
{
	private $_html = '';
	private $_postErrors = array();
	public $context;

	function __construct($dontTranslate = false)
 	{
 	 	$this->name = 'sendtoafriend';
 	 	$this->version = '1.2';
		$this->author = 'MileBiz';
 	 	$this->tab = 'front_office_features';
		$this->need_instance = 0;
		$this->secure_key = Tools::encrypt($this->name);

		parent::__construct();

		if (!$dontTranslate)
		{
			$this->displayName = $this->l('Send to a Friend module');
			$this->description = $this->l('Allows customers to send a product link to a friend.');
 		}
	}

	public function install()
	{
	 	return (parent::install() && $this->registerHook('extraLeft') && $this->registerHook('header'));
	}

	public function uninstall()
	{
		return (parent::uninstall() && $this->unregisterHook('header') && $this->unregisterHook('extraLeft'));
	}

	public function hookExtraLeft($params)
	{
		/* Product informations */
		$product = new Product((int)Tools::getValue('id_product'), false, $this->context->language->id);
		$image = Product::getCover((int)$product->id);


		$this->context->smarty->assign(array(
			'stf_product' => $product,
			'stf_product_cover' => (int)$product->id.'-'.(int)$image['id_image'],
			'stf_secure_key' => $this->secure_key
		));

		return $this->display(__FILE__, 'sendtoafriend-extra.tpl');
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'sendtoafriend.css', 'all');
	}
}