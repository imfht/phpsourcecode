<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace libsrv;

/**
 * Service class file
 * 业务层模型类单例管理，通过类名获取类的实例，并且保证在一次PHP的运行周期内只创建一次实例
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Service.php 1 2013-04-05 20:00:06Z huan.song $
 * @package libsrv
 * @since 1.0
 */
class Service
{
	/**
	 * @var array 用于寄存全局的实例
	 */
	protected static $_instances = array();

	/**
	 * 根据业务名和类名获取类的实例，适用于类的构造方法没有参数，如果类的构造方法有参数，不能只通过类名区分不同的类
	 * @param string $className
	 * @param string $serviceName
	 * @return \libsrv\AbstractService
	 */
	public static function getInstance($className, $serviceName)
	{
		if (!self::has($className, $serviceName)) {
			$className = $serviceName . '\\services\\' . $className;
			self::set($className, $serviceName, new $className());
		}

		return self::get($className, $serviceName);
	}

	/**
	 * 通过类名获取类的实例
	 * @param string $className
	 * @param string $serviceName
	 * @return \libsrv\AbstractService
	 */
	public static function get($className, $serviceName)
	{
		if (self::has($className, $serviceName)) {
			return self::$_instances[$serviceName][$className];
		}

		return null;
	}

	/**
	 * 设置类名和类的实例
	 * @param string $className
	 * @param string $serviceName
	 * @param \libsrv\AbstractService $instance
	 * @return void
	 */
	public static function set($className, $serviceName, AbstractService $instance)
	{
		self::$_instances[$serviceName][$className] = $instance;
	}

	/**
	 * 通过类名删除类的实例
	 * @param string $className
	 * @param string $serviceName
	 * @return void
	 */
	public static function remove($className, $serviceName)
	{
		if (self::has($className, $serviceName)) {
			unset(self::$_instances[$serviceName][$className]);
		}
	}

	/**
	 * 通过类名判断类的实例是否已经存在
	 * @param string $className
	 * @param string $serviceName
	 * @return boolean
	 */
	public static function has($className, $serviceName)
	{
		return isset(self::$_instances[$serviceName][$className]);
	}
}
