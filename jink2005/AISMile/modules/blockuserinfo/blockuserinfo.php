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

class BlockUserInfo extends Module
{
	public function __construct()
	{
		$this->name = 'blockuserinfo';
		$this->tab = 'front_office_features';
		$this->version = 0.1;
		$this->author = 'MileBiz';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('User info block');
		$this->description = $this->l('Adds a block that displays information about the customer.');
	}

	public function install()
	{
		return (parent::install() AND $this->registerHook('top') AND $this->registerHook('header'));
	}

	/**
	* Returns module content for header
	*
	* @param array $params Parameters
	* @return string Content
	*/
	public function hookTop($params)
	{
		if (!$this->active)
			return;

		$this->smarty->assign(array(
			'cart' => $this->context->cart,
			'cart_qties' => $this->context->cart->nbProducts(),
			'logged' => $this->context->customer->isLogged(),
			'customerName' => ($this->context->customer->logged ? $this->context->customer->firstname.' '.$this->context->customer->lastname : false),
			'firstName' => ($this->context->customer->logged ? $this->context->customer->firstname : false),
			'lastName' => ($this->context->customer->logged ? $this->context->customer->lastname : false),
			'order_process' => Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order'
		));
		return $this->display(__FILE__, 'blockuserinfo.tpl');
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blockuserinfo.css', 'all');
	}
}


