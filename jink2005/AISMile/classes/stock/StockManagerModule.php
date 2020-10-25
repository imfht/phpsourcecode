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
abstract class StockManagerModuleCore extends Module
{
	public $stock_manager_class;

	public function install()
	{
		return (parent::install() && $this->registerHook('stockManager') );
	}

	public function hookStockManager()
	{
		$class_file = _PS_MODULE_DIR_.'/'.$this->name.'/'.$this->stock_manager_class.'.php';

		if (!isset($this->stock_manager_class) || !file_exists($class_file))
			die(sprintf(Tools::displayError('Incorrect Stock Manager class [%s]'), $this->stock_manager_class));

		require_once($class_file);

		if (!class_exists($this->stock_manager_class))
			die(sprintf(Tools::displayError('Stock Manager class not found [%s]'), $this->stock_manager_class));

		$class = $this->stock_manager_class;
		if (call_user_func(array($class, 'isAvailable')))
			return new $class();

		return false;
	}
}