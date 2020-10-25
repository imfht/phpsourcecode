<?php
/**
 * Yesf自用事件
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Swoole
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Event;

use ReflectionClass;
use ReflectionNamedType;
use Yesf\DI\Container;
use Yesf\Yesf;
use Yesf\Utils;
use Yesf\Log\Logger;
use Yesf\Http\Response;
use Yesf\Connection\Pool;

class Internal {
	/**
	 * 内部事件
	 * 
	 * @access public
	 */
	public static function onWorkerStart() {
		Yesf::app()->loadEnvConfig();
		Yesf::loadProjectConfig();
		Logger::init();
		Response::init();
		Response::initInWorker();
		Utils::setRDAlias();
		Utils::setCacheAlias();
		Pool::init();
		// Configuration
		self::callConfiguration();
	}
	/**
	 * 内部事件
	 * 
	 * @access public
	 */
	public static function onCreate() {
		Utils::setRouterAlias();
		Utils::setSessionAlias();
	}
	/**
	 * Configuration
	 * 
	 * @access private
	 */
	private static function callConfiguration() {
		$container = Container::getInstance();
		$className = Yesf::app()->getConfig('namespace', Yesf::CONF_PROJECT) . 'Configuration';
		if ($container->has($className)) {
			$clazz = $container->get($className);
			$methods = (new ReflectionClass($clazz))->getMethods();
			foreach ($methods as $method) {
				if (strpos($method->name, 'set') !== 0) {
					continue;
				}
				$params = $method->getParameters();
				$init_params = [];
				foreach ($params as $param) {
					$type = $param->getType();
					if (class_exists('ReflectionNamedType') && $type instanceof ReflectionNamedType) {
						$typeName = $type->getName();
					} else {
						$typeName = $type->__toString();
					}
					if ($type->isBuiltin()) {
						$value = null;
						settype($value, $typeName);
						$init_params[] = $value;
					} else {
						$init_params[] = $container->get($typeName);
					}
				}
				$method->invokeArgs($clazz, $init_params);
			}
		}
	}
}