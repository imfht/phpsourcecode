<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

/*
 * StockManagerFactory : factory of stock manager
 * @since 1.5.0
 */
class StockManagerFactoryCore
{
	/**
	 * @var $stock_manager : instance of the current StockManager.
	 */
	protected static $stock_manager;

	/**
	 * Returns a StockManager
	 *
	 * @return StockManagerInterface
	 */
	public static function getManager()
	{
		if (!isset(StockManagerFactory::$stock_manager))
		{
			$stock_manager = StockManagerFactory::execHookStockManagerFactory();
			if (!($stock_manager instanceof StockManagerInterface))
				$stock_manager = new StockManager();
			StockManagerFactory::$stock_manager = $stock_manager;
		}
		return StockManagerFactory::$stock_manager;
	}

	/**
	 *  Looks for a StockManager in the modules list.
	 *
	 *  @return StockManagerInterface
	 */
	public static function execHookStockManagerFactory()
	{
		$modules_infos = Hook::getModulesFromHook(Hook::getIdByName('stockManager'));
		$stock_manager = false;

		foreach ($modules_infos as $module_infos)
		{
			$module_instance = Module::getInstanceByName($module_infos['name']);

			if (is_callable(array($module_instance, 'hookStockManager')))
				$stock_manager = $module_instance->hookStockManager();

			if ($stock_manager)
				break;
		}

		return $stock_manager;
	}
}