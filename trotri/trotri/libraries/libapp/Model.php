<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace libapp;

use tfc\mvc\Mvc;

/**
 * Model class file
 * 业务类单例管理，通过类名获取类的实例，并且保证在一次PHP的运行周期内只创建一次实例
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Model.php 1 2013-04-05 20:00:06Z huan.song $
 * @package libapp
 * @since 1.0
 */
class Model
{
	/**
	 * @var array 用于寄存全局的实例
	 */
	protected static $_instances = array();

	/**
	 * 根据模块名和类名获取类的实例，适用于类的构造方法没有参数，如果类的构造方法有参数，不能只通过类名区分不同的类
	 * @param string $className
	 * @param string $moduleName
	 * @return \libapp\BaseModel
	 */
	public static function getInstance($className, $moduleName = '')
	{
		if (($moduleName = trim($moduleName)) === '') {
			$moduleName = Mvc::$module;
		}

		if (!self::has($className, $moduleName)) {
			$className = 'modules\\' . $moduleName . '\\model\\' . $className;
			self::set($className, $moduleName, new $className());
		}

		return self::get($className, $moduleName);
	}

	/**
	 * 通过类名获取类的实例
	 * @param string $className
	 * @param string $moduleName
	 * @return \libapp\BaseModel
	 */
	public static function get($className, $moduleName)
	{
		if (self::has($className, $moduleName)) {
			return self::$_instances[$moduleName][$className];
		}

		return null;
	}

	/**
	 * 设置类名和类的实例
	 * @param string $className
	 * @param string $moduleName
	 * @param \libapp\BaseModel $instance
	 * @return void
	 */
	public static function set($className, $moduleName, BaseModel $instance)
	{
		self::$_instances[$moduleName][$className] = $instance;
	}

	/**
	 * 通过类名删除类的实例
	 * @param string $className
	 * @param string $moduleName
	 * @return void
	 */
	public static function remove($className, $moduleName)
	{
		if (self::has($className, $moduleName)) {
			unset(self::$_instances[$moduleName][$className]);
		}
	}

	/**
	 * 通过类名判断类的实例是否已经存在
	 * @param string $className
	 * @param string $moduleName
	 * @return boolean
	 */
	public static function has($className, $moduleName)
	{
		return isset(self::$_instances[$moduleName][$className]);
	}
}
