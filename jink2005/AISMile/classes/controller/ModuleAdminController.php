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
abstract class ModuleAdminControllerCore extends AdminController
{
	/**
	 * @var Module
	 */
	public $module;
	
	public function __construct()
	{
		$this->controller_type = 'moduleadmin';
		
		parent::__construct();

		$tab = new Tab($this->id);
		if (!$tab->module)
			throw new MileBizException('Admin tab '.get_class($this).' is not a module tab');

		$this->module = Module::getInstanceByName($tab->module);
		if (!$this->module->id)
			throw new MileBizException("Module {$tab->module} not found");
	}

	public function createTemplate($tpl_name)
	{
		if (file_exists($this->getTemplatePath().$this->override_folder.$tpl_name) && $this->viewAccess())
			return $this->context->smarty->createTemplate($this->getTemplatePath().$this->override_folder.$tpl_name, $this->context->smarty);

		return parent::createTemplate($tpl_name);
	}

	/**
	 * Get path to back office templates for the module
	 *
	 * @return string
	 */
	public function getTemplatePath()
	{
		return _PS_MODULE_DIR_.$this->module->name.'/views/templates/admin/';
	}
}
