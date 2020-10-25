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

class BlockPermanentLinks extends Module
{
	public function __construct()
	{
		$this->name = 'blockpermanentlinks';
		$this->tab = 'front_office_features';
		$this->version = 0.1;
		$this->author = 'MileBiz';
		$this->need_instance = 0;

		parent::__construct();
		
		$this->displayName = $this->l('Permanent links block');
		$this->description = $this->l('Adds a block that displays permanent links such as sitemap, contact, etc.');
	}

	public function install()
	{
			return (parent::install() && $this->registerHook('top') && $this->registerHook('header'));
	}

	/**
	* Returns module content for header
	*
	* @param array $params Parameters
	* @return string Content
	*/
	public function hookTop($params)
	{
		return $this->display(__FILE__, 'blockpermanentlinks-header.tpl');
	}

	/**
	* Returns module content for left column
	*
	* @param array $params Parameters
	* @return string Content
	*/
	public function hookLeftColumn($params)
	{
		return $this->display(__FILE__, 'blockpermanentlinks.tpl');
	}

	public function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}

	public function hookFooter($params)
	{
		return $this->display(__FILE__, 'blockpermanentlinks-footer.tpl');
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blockpermanentlinks.css', 'all');
	}
}


