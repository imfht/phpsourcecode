<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/**
 * @since 1.5.0
 */
class ModuleFrontControllerCore extends FrontController
{
	/**
	 * @var Module
	 */
	public $module;

	public function __construct()
	{
		$this->controller_type = 'modulefront';
		
		$this->module = Module::getInstanceByName(Tools::getValue('module'));
		if (!$this->module->active)
			Tools::redirect('index');
		$this->page_name = 'module-'.$this->module->name.'-'.Dispatcher::getInstance()->getController();

		parent::__construct();
	}

	/**
	 * Assign module template
	 *
	 * @param string $template
	 */
	public function setTemplate($template)
	{
		if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/'.$this->module->name.'/'.$template))
			$this->template = _PS_THEME_DIR_.'modules/'.$this->module->name.'/'.$template;
		elseif (Tools::file_exists_cache($this->getTemplatePath().$template))
			$this->template = $this->getTemplatePath().$template;
		else
			throw new MileBizException("Template '$template'' not found");
	}

	/**
	 * Get path to front office templates for the module
	 *
	 * @return string
	 */
	public function getTemplatePath()
	{
		return _PS_MODULE_DIR_.$this->module->name.'/views/templates/front/';
	}
}
