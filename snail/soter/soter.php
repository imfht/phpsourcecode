<?php
/*
 * Copyright 2017 Soter(狂奔的蜗牛 672308444@163.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Soter
 *
 * An open source application development framework for PHP 5.3.0 or newer
 *
 * @package       Soter
 * @author        狂奔的蜗牛
 * @email         672308444@163.com
 * @copyright     Copyright (c) 2015 - 2017, 狂奔的蜗牛, Inc.
 * @link          http://git.oschina.net/snail/soter
 * @since         v1.1.32
 * @createdtime   2017-03-27 16:47:15
 */
 

/**
 * @property Soter_Config $soterConfig
 */
class Soter {
	private static $soterConfig;
	/**
	 * 包类库自动加载器
	 * @param type $className
	 */
	public static function classAutoloader($className) {
		$config = self::$soterConfig;
		$className = str_replace(array('\\', '_'), '/', $className);
		foreach (self::$soterConfig->getPackages() as $path) {
			if (file_exists($filePath = $path . $config->getClassesDirName() . '/' . $className . '.php')) {
				\Sr::includeOnce($filePath);
				break;
			}
		}
	}
	/**
	 * 初始化框架配置
	 * @return \Soter_Config
	 */
	public static function initialize() {
		self::$soterConfig = new \Soter_Config();
		//注册类自动加载
		if (function_exists('__autoload')) {
			spl_autoload_register('__autoload');
		}
		spl_autoload_register(array('Soter', 'classAutoloader'));
		//清理魔法转义
		if (get_magic_quotes_gpc()) {
			$stripList = array('_GET', '_POST', '_COOKIE');
			foreach ($stripList as $val) {
				global $$val;
				$$val = \Sr::stripSlashes($$val);
			}
		}
		return self::$soterConfig;
	}
	/**
	 * 获取运行配置
	 * @return Soter_Config
	 */
	public static function &getConfig() {
		return self::$soterConfig;
	}
	/**
	 * 运行调度
	 */
	public static function run() {
		if (\Sr::isPluginMode()) {
			self::runPlugin();
		} elseif (\Sr::isCli()) {
			self::runCli();
		} else {
			$canRunWeb = !\Sr::config()->getIsMaintainMode();
			if (!$canRunWeb) {
				foreach (\Sr::config()->getMaintainIpWhitelist() as $ip) {
					$info = explode('/', $ip);
					$netmask = empty($info[1]) ? '32' : $info[1];
					if (\Sr::ipInfo(\Sr::clientIp() . '/' . $netmask, 'netaddress') == \Sr::ipInfo($info[0] . '/' . $netmask, 'netaddress')) {
						$canRunWeb = true;
						break;
					}
				}
			}
			if ($canRunWeb) {
				self::runWeb();
			} else {
				$handle = \Sr::config()->getMaintainModeHandle();
				if (is_object($handle)) {
					$handle->handle();
				}
			}
		}
	}
	private static function initSession() {
		$config = self::getConfig();
		//session初始化
		$sessionConfig = $config->getSessionConfig();
		@ini_set('session.auto_start', 0);
		@ini_set('session.gc_probability', 1);
		@ini_set('session.gc_divisor', 100);
		@ini_set('session.gc_maxlifetime', $sessionConfig['lifetime']);
		@ini_set('session.referer_check', '');
		@ini_set('session.entropy_file', '/dev/urandom');
		@ini_set('session.entropy_length', 16);
		@ini_set('session.use_cookies', 1);
		@ini_set('session.use_only_cookies', 1);
		@ini_set('session.use_trans_sid', 0);
		@ini_set('session.hash_function', 1);
		@ini_set('session.hash_bits_per_character', 5);
		session_cache_limiter('nocache');
		session_set_cookie_params(
			$sessionConfig['lifetime'], $sessionConfig['cookie_path'], preg_match('/^[^\\.]+$/', \Sr::server('HTTP_HOST')) ? null : $sessionConfig['cookie_domain']
		);
		if (!empty($sessionConfig['session_save_path'])) {
			session_save_path($sessionConfig['session_save_path']);
		}
		session_name($sessionConfig['session_name']);
		register_shutdown_function('session_write_close');
		//session托管检测
		$sessionHandle = $config->getSessionHandle();
		if ($sessionHandle && $sessionHandle instanceof Soter_Session) {
			$sessionHandle->init();
		}
		if ($sessionConfig['autostart']) {
			\Sr::sessionStart();
		}
		//session初始化完毕
	}
	/**
	 * web模式运行
	 * @throws Soter_Exception_404
	 */
	private static function runWeb() {
		$config = self::getConfig();
		$class = '';
		$method = '';
		foreach ($config->getRouters() as $router) {
			$route = $router->find($config->getRequest());
			if ($route->found()) {
				$config->setRoute($route);
				$class = $route->getController();
				$method = $route->getMethod();
				break;
			}
		}
		if (empty($route)) {
			throw new \Soter_Exception_500('none router was found in configuration');
		}
		$_route = \Sr::config()->getRoute();
		//当前域名有绑定hmvc模块,需要处理hmvc模块
		if ($hmvcModuleName = \Sr::config()->getCurrentDomainHmvcModuleNname()) {
			if (\Soter::checkHmvc($hmvcModuleName, false)) {
				$_route->setHmvcModuleName($hmvcModuleName);
				$_route->setFound(true);
			}
		}
		if (empty($class)) {
			$class = $config->getControllerDirName() . '_' . $config->getDefaultController();
			$_route->setController($class);
		}
		if (empty($method)) {
			$method = $config->getMethodPrefix() . $config->getDefaultMethod();
			$_route->setMethod($method);
		}
		$config->setRoute($_route);
		if (!\Sr::classIsExists($class)) {
			throw new \Soter_Exception_404('Controller [ ' . $class . ' ] not found');
		}
		//初始化session
		self::initSession();
		$controllerObject = \Sr::factory($class);
		if (!($controllerObject instanceof Soter_Controller)) {
			throw new \Soter_Exception_404('[ ' . $class . ' ] not a valid Soter_Controller');
		}
		//前置方法检查执行
		if (method_exists($controllerObject, 'before')) {
			$controllerObject->before(str_replace($config->getMethodPrefix(), '', $method), $route->getArgs());
		}
		//方法检测
		if (!method_exists($controllerObject, $method)) {
			throw new \Soter_Exception_404('Method [ ' . $class . '->' . $method . '() ] not found');
		}
		//方法缓存检测
		$cacheClassName = preg_replace('/^' . \Sr::config()->getControllerDirName() . '_/', '', $class);
		$cacheMethodName = preg_replace('/^' . \Sr::config()->getMethodPrefix() . '/', '', $method);
		$methodKey = $cacheClassName . '::' . $cacheMethodName;
		$cacheMethodConfig = $config->getMethodCacheConfig();
		if (!empty($cacheMethodConfig) && \Sr::arrayKeyExists($methodKey, $cacheMethodConfig) && $cacheMethodConfig[$methodKey]['cache'] && ($cacheMethoKey = $cacheMethodConfig[$methodKey]['key']())) {
			if (!($contents = \Sr::cache()->get($cacheMethoKey))) {
				@ob_start();
				$response = call_user_func_array(array($controllerObject, $method), $route->getArgs());
				$contents = @ob_get_contents();
				@ob_end_clean();
				$contents .= is_array($response) ? \Sr::view()->set($response)->load("$cacheClassName/$cacheMethodName") : $response;
				\Sr::cache()->set($cacheMethoKey, $contents, $cacheMethodConfig[$methodKey]['time']);
			}
		} else {
			if (method_exists($controllerObject, 'after')) {
				//如果有后置方法，这里应该捕获输出然后传递给后置方法处理
				@ob_start();
				$response = call_user_func_array(array($controllerObject, $method), $route->getArgs());
				$contents = @ob_get_contents();
				@ob_end_clean();
				$contents .= is_array($response) ? \Sr::view()->set($response)->load("$cacheClassName/$cacheMethodName") : $response;
			} else {
				$response = call_user_func_array(array($controllerObject, $method), $route->getArgs());
				$contents = is_array($response) ? \Sr::view()->set($response)->load("$cacheClassName/$cacheMethodName") : $response;
			}
		}
		//后置方法检查执行
		if (method_exists($controllerObject, 'after')) {
			echo $controllerObject->after(str_replace($config->getMethodPrefix(), '', $method), $route->getArgs(), $contents);
		} else {
			echo $contents;
		}
	}
	/**
	 * 命令行模式运行
	 */
	private static function runCli() {
		$task = str_replace('/', '_', \Sr::getOpt('task'));
		$hmvcModuleName = \Sr::getOpt('hmvc');
		if (empty($task)) {
			exit('require a task name,please use --task=<taskname>' . "\n");
		}
		if (!empty($hmvcModuleName)) {
			self::checkHmvc($hmvcModuleName);
		}
		if (strpos($task, 'Soter_') === 0) {
			$taskName = $task;
		} else {
			$taskName = \Soter::getConfig()->getTaskDirName() . '_' . $task;
		}
		if (!class_exists($taskName)) {
			throw new \Soter_Exception_500('class [ ' . $taskName . ' ] not found');
		}
		$taskObject = new $taskName();
		if (!($taskObject instanceof Soter_Task)) {
			throw new \Soter_Exception_500('[ ' . $taskName . ' ] not a valid Soter_Task');
		}
		$args = \Sr::getOpt();
		$args = empty($args) ? array() : $args;
		$taskObject->_execute(new \Soter_CliArgs($args));
	}
	/**
	 * 插件模式运行
	 */
	private static function runPlugin() {
		//插件模式
	}
	/**
	 * 检测并加载hmvc模块,成功返回模块文件夹名称，失败返回false或抛出异常
	 * @staticvar array $loadedModules
	 * @param type $hmvcModuleName  hmvc模块在URI中的名称，即注册配置hmvc模块数组的键名称
	 * @throws Soter_Exception_404
	 */
	public static function checkHmvc($hmvcModuleName, $throwException = true) {
		//hmvc检测
		if (!empty($hmvcModuleName)) {
			$config = \Soter::getConfig();
			$hmvcModules = $config->getHmvcModules();
			if (empty($hmvcModules[$hmvcModuleName])) {
				if ($throwException) {
					throw new \Soter_Exception_500('Hmvc Module [ ' . $hmvcModuleName . ' ] not found, please check your config.');
				} else {
					return FALSE;
				}
			}
			//避免重复加载，提高性能
			static $loadedModules = array();
			$hmvcModuleDirName = $hmvcModules[$hmvcModuleName];
			if (!\Sr::arrayKeyExists($hmvcModuleName, $loadedModules)) {
				$loadedModules[$hmvcModuleName] = 1;
				//找到hmvc模块,去除hmvc模块名称，得到真正的路径
				$hmvcModulePath = $config->getApplicationDir() . $config->getHmvcDirName() . '/' . $hmvcModuleDirName . '/';
				//设置hmvc子项目目录为主目录，同时注册hmvc子项目目录到主包容器，以保证高优先级
				$config->setApplicationDir($hmvcModulePath)->addMasterPackage($hmvcModulePath)->bootstrap();
			}
			return $hmvcModuleDirName;
		}
		return FALSE;
	}
}
class Sr {
	static private function parseKey($key) {
		$_info = explode('.', $key);
		$keyStrArray = '';
		foreach ($_info as $k) {
			$keyStrArray .= "['{$k}']";
		}
		return $keyStrArray;
	}
	static function arrayGet($array, $key, $default = null) {
		return eval('return \Sr::arrayKeyExists(\'' . $key . '\',$array)?$array' . self::parseKey($key) . ':$default;');
	}
	static function arraySet(&$array, $key, $value) {
		return eval('$array' . self::parseKey($key) . '=$value;');
	}
	static function dump() {
		echo!self::isCli() ? '<pre style="line-height:1.5em;font-size:14px;">' : "\n";
		@ob_start();
		$args = func_get_args();
		empty($args) ? null : call_user_func_array('var_dump', $args);
		$html = @ob_get_clean();
		echo!self::isCli() ? htmlspecialchars($html) : $html;
		echo!self::isCli() ? "</pre>" : "\n";
	}
	static function includeOnce($filePath) {
		static $includeFiles = array();
		$key = self::realPath($filePath);
		if (!\Sr::arrayKeyExists($key, $includeFiles)) {
			include $filePath;
			$includeFiles[$key] = 1;
		}
	}
	static function realPath($path, $addSlash = false) {
		//是linux系统么？
		$unipath = PATH_SEPARATOR == ':';
		//检测一下是否是相对路径，windows下面没有:,linux下面没有/开头
		//如果是相对路径就加上当前工作目录前缀
		if (strpos($path, ':') === false && strlen($path) && $path{0} != '/') {
			$path = realpath('.') . DIRECTORY_SEPARATOR . $path;
		}
		$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
		$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
		$absolutes = array();
		foreach ($parts as $part) {
			if ('.' == $part)
				continue;
			if ('..' == $part) {
				array_pop($absolutes);
			} else {
				$absolutes[] = $part;
			}
		}
		//如果是linux这里会导致linux开头的/丢失
		$path = implode(DIRECTORY_SEPARATOR, $absolutes);
		//如果是linux，修复系统前缀
		$path = $unipath ? (strlen($path) && $path{0} != '/' ? '/' . $path : $path) : $path;
		//最后统一分隔符为/，windows兼容/
		$path = str_replace(array('/', '\\'), '/', $path);
		return $path . ($addSlash ? '/' : '');
	}
	static function isCli() {
		return PHP_SAPI == 'cli';
	}
	static function stripSlashes($var) {
		if (!get_magic_quotes_gpc()) {
			return $var;
		}
		if (is_array($var)) {
			foreach ($var as $key => $val) {
				if (is_array($val)) {
					$var[$key] = self::stripSlashes($val);
				} else {
					$var[$key] = stripslashes($val);
				}
			}
		} elseif (is_string($var)) {
			$var = stripslashes($var);
		}
		return $var;
	}
	static function business($businessName) {
		$name = \Soter::getConfig()->getBusinessDirName() . '_' . $businessName;
		$object = self::factory($name);
		if (!($object instanceof Soter_Business)) {
			throw new \Soter_Exception_500('[ ' . $name . ' ] not a valid Soter_Business');
		}
		return $object;
	}
	static function dao($daoName) {
		$name = \Soter::getConfig()->getDaoDirName() . '_' . $daoName;
		$object = self::factory($name);
		if (!($object instanceof Soter_Dao)) {
			throw new \Soter_Exception_500('[ ' . $name . ' ] not a valid Soter_Dao');
		}
		return $object;
	}
	static function model($modelName) {
		$name = \Soter::getConfig()->getModelDirName() . '_' . $modelName;
		$object = self::factory($name);
		if (!($object instanceof Soter_Model)) {
			throw new \Soter_Exception_500('[ ' . $name . ' ] not a valid Soter_Model');
		}
		return $object;
	}
	static function library($className) {
		return self::factory($className);
	}
	static function extension($className) {
		return self::factory('Soter_' . $className);
	}
	static function functions($functionFilename) {
		static $loadedFunctionsFile = array();
		if (\Sr::arrayKeyExists($functionFilename, $loadedFunctionsFile)) {
			return;
		} else {
			$loadedFunctionsFile[$functionFilename] = 1;
		}
		$config = \Soter::getConfig();
		$found = false;
		foreach ($config->getPackages() as $packagePath) {
			$filePath = $packagePath . $config->getFunctionsDirName() . '/' . $functionFilename . '.php';
			if (file_exists($filePath)) {
				self::includeOnce($filePath);
				$found = true;
				break;
			}
		}
		if (!$found) {
			throw new \Soter_Exception_500('functions file [ ' . $functionFilename . '.php ] not found');
		}
	}
	/**
	 * 超级工厂方法
	 * @param type $className      可以是完整的控制器类名，模型类名，类库类名
	 * @param type $hmvcModuleName hmvc模块名称，是配置里面的数组的键名，插件模式下才会用到这个参数
	 * @throws Soter_Exception_404
	 */
	static function factory($className, $hmvcModuleName = null) {
		if (\Sr::isPluginMode()) {
			//hmvc检测
			\Soter::checkHmvc($hmvcModuleName);
		}
		if (\Sr::strEndsWith(strtolower($className), '.php')) {
			$className = substr($className, 0, strlen($className) - 4);
		}
		$className1 = str_replace(array('\\', '/'), '_', $className);
		$className2 = str_replace(array('/', '_'), '\\', $className);
		$args = func_get_args();
		$class = class_exists($className1) ? new ReflectionClass($className1) : (class_exists($className2) ? new ReflectionClass($className2) : '');
		if ($class) {
			return $class->newInstanceArgs(array_slice($args, 2));
		}
		throw new \Soter_Exception_500("class [ $className ] not found");
	}
	/**
	 * 判断是否是插件模式运行
	 * @return type
	 */
	static function isPluginMode() {
		return (defined('SOTER_RUN_MODE_PLUGIN') && SOTER_RUN_MODE_PLUGIN);
	}
	/**
	 * 1.不传递参数返回系统配置对象（Soter_Config）。<br/>
	 * 2.传递参数加载具体的配置<br/>
	 * @staticvar array $loadedConfig
	 * @param type $configName
	 * @return Soter_Config|mixed
	 */
	static function &config($configName = null, $caching = true) {
		if (empty($configName)) {
			return \Soter::getConfig();
		}
		$_info = explode('.', $configName);
		$configFileName = current($_info);
		static $loadedConfig = array();
		$cfg = null;
		if ($caching && \Sr::arrayKeyExists($configFileName, $loadedConfig)) {
			$cfg = $loadedConfig[$configFileName];
		} elseif ($filePath = \Soter::getConfig()->find($configFileName)) {
			$loadedConfig[$configFileName] = $cfg = eval('?>' . file_get_contents($filePath));
		} else {
			throw new \Soter_Exception_500('config file [ ' . $configFileName . '.php ] not found');
		}
		if ($cfg && count($_info) > 1) {
			$val = self::arrayGet($cfg, implode('.', array_slice($_info, 1)));
			return $val;
		} else {
			return $cfg;
		}
	}
	/**
	 * 解析命令行参数 $GLOBALS['argv'] 到一个数组<br>
	 * 参数形式支持:		<br>
	 * -e			<br>
	 * -e <value>		<br>
	 * --long-param		<br>
	 * --long-param=<value><br>
	 * --long-param <value><br>
	 * <value>
	 *
	 */
	static function getOpt($key = null) {
		if (!self::isCli()) {
			return null;
		}
		$noopt = array();
		static $result = array();
		static $parsed = false;
		if (!$parsed) {
			$parsed = true;
			$params = self::arrayGet($GLOBALS, 'argv', array());
			reset($params);
			while (list($tmp, $p) = each($params)) {
				if ($p{0} == '-') {
					$pname = substr($p, 1);
					$value = true;
					if ($pname{0} == '-') {
						$pname = substr($pname, 1);
						if (strpos($p, '=') !== false) {
							list($pname, $value) = explode('=', substr($p, 2), 2);
						}
					}
					$nextparm = current($params);
					if (!in_array($pname, $noopt) && $value === true && $nextparm !== false && $nextparm{0} != '-') {
						list($tmp, $value) = each($params);
					}
					$result[$pname] = $value;
				} else {
					$result[] = $p;
				}
			}
		}
		return empty($key) ? $result : (\Sr::arrayKeyExists($key, $result) ? $result[$key] : null);
	}
	static function get($key = null, $default = null, $xssClean = false) {
		$value = is_null($key) ? $_GET : self::arrayGet($_GET, $key, $default);
		return $xssClean ? self::xssClean($value) : $value;
	}
	static function getPost($key, $default = null, $xssClean = false) {
		$getValue = self::arrayGet($_GET, $key);
		$value = is_null($getValue) ? self::arrayGet($_POST, $key, $default) : $getValue;
		return $xssClean ? self::xssClean($value) : $value;
	}
	static function post($key = null, $default = null, $xssClean = false) {
		$value = is_null($key) ? $_POST : self::arrayGet($_POST, $key, $default);
		return $xssClean ? self::xssClean($value) : $value;
	}
	static function postGet($key, $default = null, $xssClean = false) {
		$postValue = self::arrayGet($_POST, $key);
		$value = is_null($postValue) ? self::arrayGet($_GET, $key, $default) : $postValue;
		return $xssClean ? self::xssClean($value) : $value;
	}
	static function session($key = null, $default = null, $xssClean = false) {
		self::sessionStart();
		$value = is_null($key) ? (empty($_SESSION) ? null : $_SESSION) : self::arrayGet($_SESSION, $key, $default);
		return $xssClean ? self::xssClean($value) : $value;
	}
	static function sessionSet($key, $value) {
		self::sessionStart();
		if (is_array($key)) {
			$_SESSION = array_merge($_SESSION, $key);
		} else {
			self::arraySet($_SESSION, $key, $value);
		}
	}
	static function sessionUnset($key = null) {
		if (is_null($key)) {
			unset($_SESSION);
		} else {
			eval('unset($_SESSION' . self::parseKey($key) . ');');
		}
	}
	static function server($key = null, $default = null) {
		return is_null($key) ? $_SERVER : self::arrayGet($_SERVER, strtoupper($key), $default);
	}
	/**
	 * 获取原始的POST数据，即php://input获取到的
	 * @return type
	 */
	static function postRawBody() {
		return file_get_contents('php://input');
	}
	/**
	 * 获取一个cookie
	 * 提醒:
	 * 该方法会在key前面加上系统配置里面的getCookiePrefix()
	 * 如果想不加前缀，获取原始key的cookie，可以使用方法：Sr::cookieRaw();
	 * @return type
	 */
	static function cookie($key = null, $default = null, $xssClean = false) {
		$key = is_null($key) ? null : \Sr::config()->getCookiePrefix() . $key;
		$value = self::cookieRaw($key, $default, $xssClean);
		return $xssClean ? self::xssClean($value) : $value;
	}
	static function cookieRaw($key = null, $default = null, $xssClean = false) {
		$value = is_null($key) ? $_COOKIE : self::arrayGet($_COOKIE, $key, $default);
		return $xssClean ? self::xssClean($value) : $value;
	}
	/**
	 * 设置一个cookie，该方法会在key前面加上系统配置里面的getCookiePrefix()前缀<br>
	 * 如果不想加前缀，可以使用方法：Sr::setCookieRaw()<br>
	 * 或者设置前缀为空那么Sr::cookie和Sr::cookieRaw效果一样。前缀默认就是空。
	 */
	static function setCookie($key, $value, $life = null, $path = '/', $domian = null, $http_only = false) {
		$key = \Sr::config()->getCookiePrefix() . $key;
		return self::setCookieRaw($key, $value, $life, $path, $domian, $http_only);
	}
	static function setCookieRaw($key, $value, $life = null, $path = '/', $domian = null, $httpOnly = false) {
		if (!\Sr::isCli()) {
			header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		}
		if (!is_null($domian)) {
			$autoDomain = $domian;
		} else {
			$host = self::server('HTTP_HOST');
			$is_ip = preg_match('/^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)$/', $host);
			$notRegularDomain = preg_match('/^[^\\.]+$/', $host);
			if ($is_ip) {
				$autoDomain = $host;
			} elseif ($notRegularDomain) {
				$autoDomain = NULL;
			} else {
				$autoDomain = '.' . $host;
			}
		}
		setcookie($key, $value, ($life ? $life + time() : null), $path, $autoDomain, (self::server('SERVER_PORT') == 443 ? 1 : 0), $httpOnly);
		$_COOKIE[$key] = $value;
	}
	static function xssClean($var) {
		if (is_array($var)) {
			foreach ($var as $key => $val) {
				if (is_array($val)) {
					$var[$key] = self::xssClean($val);
				} else {
					$var[$key] = self::xssClean0($val);
				}
			}
		} elseif (is_string($var)) {
			$var = self::xssClean0($var);
		}
		return $var;
	}
	private static function xssClean0($data) {
		// Fix &entity\n;
		$data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
		$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
		// Remove any attribute starting with "on" or xmlns
		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);
		// Remove javascript: and vbscript: protocols
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);
		// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);
		// Remove namespaced elements (we do not need them)
		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
		do {
			// Remove really unwanted tags
			$old_data = $data;
			$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|iframe|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
		} while ($old_data !== $data);
		// we are done...
		return $data;
	}
	/**
	 * 服务器的hostname
	 * @return type
	 */
	static function hostname() {
		return function_exists('gethostname') ? gethostname() : (function_exists('php_uname') ? php_uname('n') : 'unknown');
	}
	/**
	 * 服务器的ip
	 */
	static function serverIp() {
		return self::isCli() ? gethostbyname(self::hostname()) : \Sr::server('SERVER_ADDR');
	}
	/**
	 * 访问者的ip
	 */
	static function clientIp($source = array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'), $check = array('HTTP_X_FORWARDED_FOR')) {
		foreach ($source as $k => $v) {
			$source[$k] = strtoupper($v);
		}
		foreach ($check as $k => $v) {
			$check[$k] = strtoupper($v);
		}
		foreach ($source as $v) {
			if ($ip = self::server($v)) {
				if (!in_array($v, $check)) {
					return $ip;
				}if ($ip = self::checkClientIp($v)) {
					return $ip;
				} else {
					break;
				}
			}
		}
		return "Unknown";
	}
	private static function checkClientIp($ip) {
		if (empty($ip)) {
			return false;
		}
		$whitelist = \Sr::config()->getBackendServerIpWhitelist();
		foreach ($whitelist as $okayIp) {
			if ($okayIp == $ip) {
				return $ip;
			}
		}
		return FALSE;
	}
	static function strBeginsWith($str, $sub) {
		return ( substr($str, 0, strlen($sub)) == $sub );
	}
	static function strEndsWith($str, $sub) {
		return ( substr($str, strlen($str) - strlen($sub)) == $sub );
	}
	/**
	 * 获取IP段信息<br>
	 * $ipAddr格式：192.168.1.10/24、192.168.1.10/32<br>
	 * 传入Ip地址对Ip段地址进行处理得到相关的信息<br>
	 * 1.没有$key时，返回数组：array(<br>
	 * netmask=>网络掩码<br>
	 * count=>网络可用IP数目<br>
	 * start=>可用IP开始<br>
	 * end=>可用IP结束<br>
	 * netaddress=>网络地址<br>
	 * broadcast=>广播地址<br>
	 * )<br>
	 * 2.有$key时返回$key对应的值，$key是上面数组的键。
	 */
	static function ipInfo($ipAddr, $key = null) {
		$ipAddr = str_replace(" ", "", $ipAddr);    //去除字符串中的空格
		$arr = explode('/', $ipAddr); //对IP段进行解剖
		$ipAddr = $arr[0];    //得到IP地址
		$ipAddrArr = explode('.', $ipAddr);
		foreach ($ipAddrArr as $k => $v) {
			$ipAddrArr[$k] = intval($v); //去掉192.023.20.01其中的023的0
		}
		$ipAddr = implode('.', $ipAddrArr); //修正后的ip地址
		$netbits = intval((\Sr::arrayKeyExists(1, $arr) ? $arr[1] : 0));   //得到掩码位
		$subnetMask = long2ip(ip2long("255.255.255.255") << (32 - $netbits));
		$ip = ip2long($ipAddr);
		$nm = ip2long($subnetMask);
		$nw = ($ip & $nm);
		$bc = $nw | (~$nm);
		$ips = array();
		$ips['netmask'] = long2ip($nm);     //网络掩码
		$ips['count'] = ($bc - $nw - 1);      //可用IP数目
		if ($ips['count'] <= 0) {
			$ips['count'] += 4294967296;
		}
		if ($netbits == 32) {
			$ips['count'] = 0;      //当$netbits是32的时候可用数目是-1，这里修正为1
			$ips['start'] = long2ip($ip);    //可用IP开始
			$ips['end'] = long2ip($ip);      //可用IP结束
		} else {
			$ips['start'] = long2ip($nw + 1);    //可用IP开始
			$ips['end'] = long2ip($bc - 1);      //可用IP结束
		}
		$bc = sprintf('%u', $bc);    //或者采用此方法转换成无符号的，修复32位操作系统中long2ip后会出现负数
		$nw = sprintf('%u', $nw);
		$ips['netaddress'] = long2ip($nw);       //网络地址
		$ips['broadcast'] = long2ip($bc);       //广播地址
		return is_null($key) ? $ips : $ips[$key];
	}
	/**
	 *
	 * 获取数据库操作对象
	 * @staticvar array $instances   数据库单例容器
	 * @param type $group             配置组名称
	 * @param type $isNewInstance     是否刷新单例
	 * @return \Soter_Database_ActiveRecord
	 * @throws Soter_Exception_Database
	 */
	private static $dbInstances = array();
	static function clearDbInstances($key = null) {
		if (!is_null($key)) {
			unset(self::$dbInstances[$key]);
		} else {
			self::$dbInstances = array();
		}
	}
	/**
	 * @return \Soter_Database_ActiveRecord
	 */
	static function &db($group = '', $isNewInstance = false) {
		if (is_array($group)) {
			ksort($group);
			$groupString = json_encode($group);
			$key = md5($groupString);
			if (!\Sr::arrayKeyExists($key, self::$dbInstances) || $isNewInstance) {
				$group['group'] = $groupString;
				self::$dbInstances[$key] = new \Soter_Database_ActiveRecord($group);
			}
			return self::$dbInstances[$key];
		} else {
			$config = self::config()->getDatabseConfig();
			if (empty($config)) {
				throw new \Soter_Exception_Database('database configuration is empty , did you forget to use "->setDatabseConfig()" in index.php ?');
			}
			if (empty($group)) {
				$group = $config['default_group'];
			}
			if (!\Sr::arrayKeyExists($group, self::$dbInstances) || $isNewInstance) {
				$config = self::config()->getDatabseConfig($group);
				if (empty($config)) {
					throw new \Soter_Exception_Database('unknown database config group [ ' . $group . ' ]');
				}
				$config['group'] = $group;
				self::$dbInstances[$group] = new \Soter_Database_ActiveRecord($config);
			}
			return self::$dbInstances[$group];
		}
	}
	static function createSqlite3Database($path) {
		return new \PDO('sqlite:' . $path);
	}
	/**
	 * 获取当前UNIX毫秒时间戳
	 * @return float
	 */
	static function microtime() {
		// 获取当前毫秒时间戳
		list ($s1, $s2) = explode(' ', microtime());
		$currentTime = (float) sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
		return $currentTime;
	}
	/**
	 * 屏蔽路径中系统的绝对路径部分，转换为安全的用于显示
	 * @param type $path
	 * @return string
	 */
	static function safePath($path) {
		if (!$path) {
			return '';
		}
		$path = self::realPath($path);
		$siteRoot = self::realPath(self::server('DOCUMENT_ROOT'));
		$_path = str_replace($siteRoot, '', $path);
		$relPath = str_replace($siteRoot, '', rtrim(self::config()->getApplicationDir(), '/'));
		return '~APPPATH~' . str_replace($relPath, '', $_path);
	}
	/**
	 * 获取缓存操作对象
	 * @param type $cacheType
	 * @return Soter_Cache
	 */
	static function cache($cacheType = null) {
		return self::config()->getCacheHandle($cacheType);
	}
	/**
	 * 删除文件夹和子文件夹
	 * @param string $dirPath   文件夹路径
	 * @param type $includeSelf 是否保留最父层文件夹
	 * @return boolean
	 */
	static function rmdir($dirPath, $includeSelf = true) {
		if (empty($dirPath)) {
			return false;
		}
		$dirPath = self::realPath($dirPath) . '/';
		foreach (scandir($dirPath) as $value) {
			if ($value == '.' || $value == '..') {
				continue;
			}
			$path = $dirPath . $value;
			if (is_dir($path)) {
				self::rmdir($path);
				@rmdir($path);
			} else {
				@unlink($path);
			}
		}
		if ($includeSelf) {
			@rmdir($dirPath);
		}
		return true;
	}
	static function view() {
		static $view;
		if (!$view) {
			$view = new \Soter_View();
		}
		return $view;
	}
	/**
	 * 获取入口文件所在目录url路径。
	 * 只能在web访问时使用，在命令行下面会抛出异常。
	 * @param type $subpath  子路径或者文件路径，如果非空就会被附加在入口文件所在目录的后面
	 * @return type
	 * @throws Exception
	 */
	static function urlPath($subpath = null, $addSlash = true) {
		if (self::isCli()) {
			throw new \Soter_Exception_500('urlPath() can not be used in cli mode');
		} else {
			$old_path = getcwd();
			$root = str_replace(array("/", "\\"), '/', self::server('DOCUMENT_ROOT'));
			chdir($root);
			$root = getcwd();
			$root = str_replace(array("/", "\\"), '/', $root);
			chdir($old_path);
			$path = str_replace(array("/", "\\"), '/', realpath('.') . ($subpath ? '/' . trim($subpath, '/\\') : ''));
			$path = self::realPath($path) . ($addSlash ? '/' : '');
			return preg_replace('|^' . self::realPath($root) . '|', '', $path);
		}
	}
	/**
	 * 生成控制器方法的url
	 * @param type $action   控制器方法
	 * @param type $getData  get传递的参数数组，键值对，键是参数名，值是参数值
	 * @return string
	 */
	static function url($action = '', $getData = array()) {
		$config = \Sr::config();
		$hmvcModuleName = $config->getCurrentDomainHmvcModuleNname(); //当前域名绑定的hmvc模块名称
		//访问的是hmvc模块且绑定了当前域名，且是DomainOnly的，就去掉开头的模块名称
		if ($hmvcModuleName && $config->hmvcIsDomainOnly($hmvcModuleName)) {
			$action = preg_replace('|^' . $hmvcModuleName . '/?|', '/', $action);
		}
		$index = self::config()->getIsRewrite() ? '' : self::config()->getIndexName() . '/';
		$url = self::urlPath($index . $action);
		$url = rtrim($url, '/');
		$url = $index ? $url : ($action ? $url : $url . '/');
		if (!empty($getData)) {
			$url = $url . '?';
			foreach ($getData as $k => $v) {
				$url .= $k . '=' . urlencode($v) . '&';
			}
			$url = rtrim($url, '&');
		}
		return $url;
	}
	/**
	 * $source_data和$map的key一致，$map的value是返回数据的key
	 * 根据$map的key读取$source_data中的数据，结果是以map的value为key的数数组
	 *
	 * @param Array $map 字段映射数组,格式：array('表单name名称'=>'表字段名称',...)
	 */
	static function readData(Array $map, $sourceData = null) {
		$data = array();
		$formdata = is_null($sourceData) ? \Sr::post() : $sourceData;
		foreach ($formdata as $formKey => $val) {
			if (\Sr::arrayKeyExists($formKey, $map)) {
				$data[$map[$formKey]] = $val;
			}
		}
		return $data;
	}
	static function checkData($data, $rules, &$returnData, &$errorMessage, &$errorKey = null, &$db = null) {
		static $checkRules;
		if (empty($checkRules)) {
			$defaultRules = array(
			    'array' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data) || !is_array($value)) {
					    return false;
				    }
				    $minOkay = true;
				    if (\Sr::arrayKeyExists(0, $args)) {
					    $minOkay = count($value) >= intval($args[0]);
				    }
				    $maxOkay = true;
				    if (\Sr::arrayKeyExists(1, $args)) {
					    $minOkay = count($value) >= intval($args[1]);
				    }
				    return $minOkay && $maxOkay;
			    }, 'notArray' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    return !is_array($value);
			    }, 'default' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (is_array($value)) {
					    $i = 0;
					    foreach ($value as $k => $v) {
						    $returnValue[$k] = empty($v) ? (\Sr::arrayKeyExists($i, $args) ? $args[$i] : $args[0]) : $v;
						    $i++;
					    }
				    } elseif (empty($value)) {
					    $returnValue = $args[0];
				    }
				    return true;
			    }, 'optional' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    $break = !isset($data[$key]);
				    return true;
			    }, 'required' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data) || empty($value)) {
					    return false;
				    }
				    $value = (array) $value;
				    foreach ($value as $v) {
					    if (empty($v)) {
						    return false;
					    }
				    }
				    return true;
			    }, 'requiredKey' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    $args[] = $key;
				    $args = array_unique($args);
				    foreach ($args as $k) {
					    if (!\Sr::arrayKeyExists($k, $data)) {
						    return false;
					    }
				    }
				    return true;
			    }, 'functions' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return true;
				    }
				    $returnValue = $value;
				    if (is_array($returnValue)) {
					    foreach ($returnValue as $k => $v) {
						    foreach ($args as $function) {
							    $returnValue[$k] = $function($v);
						    }
					    }
				    } else {
					    foreach ($args as $function) {
						    $returnValue = $function($returnValue);
					    }
				    }
				    return true;
			    }, 'xss' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return true;
				    }
				    $returnValue = \Sr::xssClean($value);
				    return true;
			    }, 'match' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data) || !\Sr::arrayKeyExists(0, $args) || !\Sr::arrayKeyExists($args[0], $data) || $value != $data[$args[0]]) {
					    return false;
				    }
				    return true;
			    }, 'equal' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data) || !\Sr::arrayKeyExists(0, $args) || $value != $args[0]) {
					    return false;
				    }
				    return true;
			    }, 'enum' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $value = (array) $value;
				    foreach ($value as $v) {
					    if (!in_array($v, $args)) {
						    return false;
					    }
				    }
				    return true;
			    }, 'unique' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    #比如unique[user.name] , unique[user.name,id:1]
				    if (!\Sr::arrayKeyExists($key, $data) || !$value || !count($args)) {
					    return false;
				    }
				    $_info = explode('.', $args[0]);
				    if (count($_info) != 2) {
					    return false;
				    }
				    $table = $_info[0];
				    $col = $_info[1];
				    if (\Sr::arrayKeyExists(1, $args)) {
					    $_id_info = explode(':', $args[1]);
					    if (count($_id_info) != 2) {
						    return false;
					    }
					    $id_col = $_id_info[0];
					    $id = $_id_info[1];
					    $id = stripos($id, '#') === 0 ? \Sr::getPost(substr($id, 1)) : $id;
					    $where = array($col => $value, "$id_col <>" => $id);
				    } else {
					    $where = array($col => $value);
				    }
				    return !$db->where($where)->from($table)->limit(0, 1)->execute()->total();
			    }, 'exists' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    #比如exists[user.name] , exists[user.name,type:1], exists[user.name,type:1,sex:#sex]
				    if (!\Sr::arrayKeyExists($key, $data) || !$value || !count($args)) {
					    return false;
				    }
				    $_info = explode('.', $args[0]);
				    if (count($_info) != 2) {
					    return false;
				    }
				    $table = $_info[0];
				    $col = $_info[1];
				    $where = array($col => $value);
				    if (count($args) > 1) {
					    foreach (array_slice($args, 1) as $v) {
						    $_id_info = explode(':', $v);
						    if (count($_id_info) != 2) {
							    continue;
						    }
						    $id_col = $_id_info[0];
						    $id = $_id_info[1];
						    $id = stripos($id, '#') === 0 ? \Sr::getPost(substr($id, 1)) : $id;
						    $where[$id_col] = $id;
					    }
				    }
				    return $db->where($where)->from($table)->limit(0, 1)->execute()->total();
			    }, 'min_len' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = \Sr::arrayKeyExists(0, $args) ? (mb_strlen($value, 'UTF-8') >= intval($args[0])) : false;
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'max_len' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = \Sr::arrayKeyExists(0, $args) ? (mb_strlen($value, 'UTF-8') <= intval($args[0])) : false;
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'range_len' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = count($args) == 2 ? (mb_strlen($value, 'UTF-8') >= intval($args[0])) && (mb_strlen($value, 'UTF-8') <= intval($args[1])) : false;
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'len' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = \Sr::arrayKeyExists(0, $args) ? (mb_strlen($value, 'UTF-8') == intval($args[0])) : false;
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'min' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = \Sr::arrayKeyExists(0, $args) && is_numeric($value) ? $value >= $args[0] : false;
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'max' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = \Sr::arrayKeyExists(0, $args) && is_numeric($value) ? $value <= $args[0] : false;
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'range' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = (count($args) == 2) && is_numeric($value) ? $value >= $args[0] && $value <= $args[1] : false;
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'alpha' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    #纯字母
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = !preg_match('/[^A-Za-z]+/', $value);
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'alpha_num' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    #纯字母和数字
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = !preg_match('/[^A-Za-z0-9]+/', $value);
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'alpha_dash' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    #纯字母和数字和下划线和-
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = !preg_match('/[^A-Za-z0-9_-]+/', $value);
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'alpha_start' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    #以字母开头
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = preg_match('/^[A-Za-z]+/', $value);
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'num' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    #纯数字
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = !preg_match('/[^0-9]+/', $value);
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'int' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    #整数
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = preg_match('/^([-+]?[1-9]\d*|0)$/', $value);
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'float' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    #小数
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = preg_match('/^([1-9]\d*|0)\.\d+$/', $value);
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'numeric' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    #数字-1，1.2，+3，4e5
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = is_numeric($value);
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'natural' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    #自然数0，1，2，3，12，333
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = preg_match('/^([1-9]\d*|0)$/', $value);
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'natural_no_zero' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    #自然数不包含0
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = preg_match('/^[1-9]\d*$/', $value);
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'email' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $args[0] = \Sr::arrayKeyExists(0, $args) && $args[0] == 'true' ? TRUE : false;
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = !empty($value) ? preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $value) : $args[0];
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'url' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $args[0] = \Sr::arrayKeyExists(0, $args) && $args[0] == 'true' ? TRUE : false;
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = !empty($value) ? preg_match('/^http[s]?:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"])*$/', $value) : $args[0];
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'qq' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $args[0] = \Sr::arrayKeyExists(0, $args) && $args[0] == 'true' ? TRUE : false;
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = !empty($value) ? preg_match('/^[1-9][0-9]{4,}$/', $value) : $args[0];
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'phone' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $args[0] = \Sr::arrayKeyExists(0, $args) && $args[0] == 'true' ? TRUE : false;
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = !empty($value) ? preg_match('/^(?:\d{3}-?\d{8}|\d{4}-?\d{7})$/', $value) : $args[0];
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'mobile' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $args[0] = \Sr::arrayKeyExists(0, $args) && $args[0] == 'true' ? TRUE : false;
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = !empty($value) ? preg_match('/^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1})|(14[0-9]{1}))+\d{8})$/', $value) : $args[0];
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'zipcode' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $args[0] = \Sr::arrayKeyExists(0, $args) && $args[0] == 'true' ? TRUE : false;
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = !empty($value) ? preg_match('/^[1-9]\d{5}(?!\d)$/', $value) : $args[0];
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'idcard' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $args[0] = \Sr::arrayKeyExists(0, $args) && $args[0] == 'true' ? TRUE : false;
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = !empty($value) ? preg_match('/^\d{14}(\d{4}|(\d{3}[xX])|\d{1})$/', $value) : $args[0];
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'ip' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $args[0] = \Sr::arrayKeyExists(0, $args) && $args[0] == 'true' ? TRUE : false;
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = !empty($value) ? preg_match('/^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)$/', $value) : $args[0];
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'chs' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $count = implode(',', array_slice($args, 1, 2));
				    $count = empty($count) ? '1,' : $count;
				    $can_empty = \Sr::arrayKeyExists(0, $args) && $args[0] == 'true';
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = !empty($value) ? preg_match('/^[\x{4e00}-\x{9fa5}]{' . $count . '}$/u', $value) : $can_empty;
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'date' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $args[0] = \Sr::arrayKeyExists(0, $args) && $args[0] == 'true' ? TRUE : false;
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = !empty($value) ? preg_match('/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/', $value) : $args[0];
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'time' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $args[0] = \Sr::arrayKeyExists(0, $args) && $args[0] == 'true' ? TRUE : false;
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = !empty($value) ? preg_match('/^(([0-1][0-9])|([2][0-3])):([0-5][0-9])(:([0-5][0-9]))$/', $value) : $args[0];
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'datetime' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $args[0] = \Sr::arrayKeyExists(0, $args) && $args[0] == 'true' ? TRUE : false;
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = !empty($value) ? preg_match('/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30))) (([0-1][0-9])|([2][0-3])):([0-5][0-9])(:([0-5][0-9]))$/', $value) : $args[0];
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }, 'reg' => function($key, $value, $data, $args, &$returnValue, &$break, &$db) {
				    if (!\Sr::arrayKeyExists($key, $data)) {
					    return false;
				    }
				    $v = (array) $value;
				    foreach ($v as $value) {
					    $okay = !empty($args[0]) ? preg_match($args[0], $value) : false;
					    if (!$okay) {
						    return false;
					    }
				    }
				    return true;
			    }
			);
			$userRules = \Sr::config()->getDataCheckRules();
			$checkRules = (is_array($userRules) && !empty($userRules)) ? array_merge($defaultRules, $userRules) : $defaultRules;
		}
		$getCheckRuleInfo = function($_rule) {
			$matches = array();
			preg_match('|([^\[]+)(?:\[(.*)\](.?))?|', $_rule, $matches);
			$matches[1] = \Sr::arrayKeyExists(1, $matches) ? $matches[1] : '';
			if ($matches[1] == 'reg') {
				$matches[3] = '';
				$matches[2] = \Sr::arrayKeyExists(2, $matches) ? array($matches[2]) : array();
			} else {
				$matches[3] = !empty($matches[3]) ? $matches[3] : ',';
				$matches[2] = \Sr::arrayKeyExists(2, $matches) ? explode($matches[3], $matches[2]) : array();
			}
			return $matches;
		};
		$returnData = $data;
		foreach ($rules as $key => $keyRules) {
			foreach ($keyRules as $rule => $message) {
				$matches = $getCheckRuleInfo($rule);
				$_v = self::arrayGet($returnData, $key);
				$_r = $matches[1];
				$args = $matches[2];
				if (!\Sr::arrayKeyExists($_r, $checkRules) || !is_callable($checkRules[$_r])) {
					throw new \Soter_Exception_500('error rule [ ' . $_r . ' ]');
				}
				$ruleFunction = $checkRules[$_r];
				$db = (is_object($db) && ($db instanceof Soter_Database_ActiveRecord) ) ? $db : \Sr::db();
				$break = false;
				$returnValue = null;
				$isOkay = $ruleFunction($key, $_v, $data, $args, $returnValue, $break, $db);
				if (!$isOkay) {
					$errorMessage = $message;
					$errorKey = $key;
					return false;
				}
				if (!is_null($returnValue)) {
					$returnData[$key] = $returnValue;
				}
				if ($break) {
					break;
				}
			}
		}
		return true;
	}
	static function sessionStart() {
		if (!self::isCli()) {
			$started = false;
			if (version_compare(phpversion(), '5.4.0', '>=')) {
				$started = session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
			} else {
				$started = session_id() === '' ? FALSE : TRUE;
			}
			if (!$started && !headers_sent()) {
				@session_start();
			}
		}
	}
	/**
	 * 分页方法
	 * @param type $total 一共多少记录
	 * @param type $page  当前是第几页
	 * @param type $pagesize 每页多少
	 * @param type $url    url是什么，url里面的{page}会被替换成页码
	 * @param array $order 分页条的组成，是一个数组，可以按着1-6的序号，选择分页条组成部分和每个部分的顺序
	 * @param int $a_count   分页条中a页码链接的总数量,不包含当前页的a标签，默认10个。
	 * @return type  String
	 * echo Sr::page(100,3,10,'?article/list/{page}',array(3,4,5,1,2,6));
	 */
	static function page($total, $page, $pagesize, $url, $order = array(1, 2, 3, 4, 5, 6), $a_count = 10) {
		$a_num = $a_count;
		$first = '首页';
		$last = '尾页';
		$pre = '上页';
		$next = '下页';
		$a_num = $a_num % 2 == 0 ? $a_num + 1 : $a_num;
		$pages = ceil($total / $pagesize);
		$curpage = intval($page) ? intval($page) : 1;
		$curpage = $curpage > $pages || $curpage <= 0 ? 1 : $curpage; #当前页超范围置为1
		$body = '<span class="page_body">';
		$prefix = '';
		$subfix = '';
		$start = $curpage - ($a_num - 1) / 2; #开始页
		$end = $curpage + ($a_num - 1) / 2;  #结束页
		$start = $start <= 0 ? 1 : $start;   #开始页超范围修正
		$end = $end > $pages ? $pages : $end; #结束页超范围修正
		if ($pages >= $a_num) {#总页数大于显示页数
			if ($curpage <= ($a_num - 1) / 2) {
				$end = $a_num;
			}//当前页在左半边补右边
			if ($end - $curpage <= ($a_num - 1) / 2) {
				$start -= floor($a_num / 2) - ($end - $curpage);
			}//当前页在右半边补左边
		}
		for ($i = $start; $i <= $end; $i++) {
			if ($i == $curpage) {
				$body .= '<a class="page_cur_page" href="javascript:void(0);"><b>' . $i . '</b></a>';
			} else {
				$body .= '<a href="' . str_replace('{page}', $i, $url) . '">' . $i . '</a>';
			}
		}
		$body .= '</span>';
		$prefix = ($curpage == 1 ? '' : '<span class="page_bar_prefix"><a href="' . str_replace('{page}', 1, $url) . '">' . $first . '</a><a href="' . str_replace('{page}', $curpage - 1, $url) . '">' . $pre . '</a></span>');
		$subfix = ($curpage == $pages ? '' : '<span class="page_bar_subfix"><a href="' . str_replace('{page}', $curpage + 1, $url) . '">' . $next . '</a><a href="' . str_replace('{page}', $pages, $url) . '">' . $last . '</a></span>');
		$info = "<span class=\"page_cur\">第{$curpage}/{$pages}页</span>";
		$id = "gsd09fhas9d" . rand(100000, 1000000);
		$go = '<script>function ekup(){if(event.keyCode==13){clkyup();}}function clkyup(){var num=document.getElementById(\'' . $id . '\').value;if(!/^\d+$/.test(num)||num<=0||num>' . $pages . '){alert(\'请输入正确页码!\');return;};location=\'' . addslashes($url) . '\'.replace(/\\{page\\}/,document.getElementById(\'' . $id . '\').value);}</script><span class="page_input_num"><input onkeyup="ekup()" type="text" id="' . $id . '" style="width:40px;vertical-align:text-baseline;padding:0 2px;font-size:10px;border:1px solid gray;"/></span><span class="page_btn_go" onclick="clkyup();" style="cursor:pointer;">转到</span>';
		$total = "<span class=\"page_total\">共{$total}条</span>";
		$pagination = array(
		    $total,
		    $info,
		    $prefix,
		    $body,
		    $subfix,
		    $go
		);
		$output = array();
		if (is_null($order)) {
			$order = array(1, 2, 3, 4, 5, 6);
		}
		foreach ($order as $key) {
			if (\Sr::arrayKeyExists($key - 1, $pagination)) {
				$output[] = $pagination[$key - 1];
			}
		}
		return $pages > 1 ? implode("", $output) : '';
	}
	static function json() {
		$args = func_get_args();
		$handle = \Sr::config()->getOutputJsonRender();
		if (is_callable($handle)) {
			return call_user_func_array($handle, $args);
		} else {
			return '';
		}
	}
	static function redirect($url, $msg = null, $time = 3, $view = null) {
		if (empty($msg) && empty($view)) {
			header('Location: ' . $url);
		} else {
			$time = intval($time) ? intval($time) : 3;
			header("refresh:{$time};url={$url}"); //单位秒
			header("Content-type: text/html; charset=utf-8");
			if (empty($view)) {
				echo $msg;
			} else {
				self::view()->set(array('msg' => $msg, 'url' => $url, 'time' => $time))->load($view);
			}
		}
		exit();
	}
	static function message($msg, $url = null, $time = 3, $view = null) {
		$time = intval($time) ? intval($time) : 3;
		if (!empty($url)) {
			header("refresh:{$time};url={$url}"); //单位秒
		}
		header("Content-type: text/html; charset=utf-8");
		if (!empty($view)) {
			self::view()->set(array('msg' => $msg, 'url' => $url, 'time' => $time))->load($view);
		} else {
			echo $msg;
		}
		exit();
	}
	public static function __callStatic($name, $arguments) {
		$methods = self::config()->getSrMethods();
		if (empty($methods[$name])) {
			throw new \Soter_Exception_500($name . ' not found in ->setSrMethods() or it is empty');
		}
		if (is_string($methods[$name])) {
			$className = $methods[$name] . '_' . self::arrayGet($arguments, 0);
			if ($className) {
				return \Sr::factory($className);
			} else {
				throw new \Soter_Exception_500($methods[$name] . '() need argument of class name ');
			}
		} elseif (is_callable($methods[$name])) {
			return call_user_func_array($methods[$name], $arguments);
		} else {
			throw new \Soter_Exception_500($name . ' unknown type of method [ ' . $name . ' ]');
		}
	}
	static function arrayKeyExists($key, $array) {
		if (empty($array) || !is_array($array)) {
			return false;
		}
		$keys = explode('.', $key);
		while (count($keys) != 0) {
			if (empty($array) || !is_array($array)) {
				return false;
			}
			$key = array_shift($keys);
			if (!array_key_exists($key, $array)) {
				return false;
			}
			$array = $array[$key];
		}
		return true;
	}
	private static function getEncryptKey($key, $attachKey) {
		$_key = $key ? $key : self::config()->getEncryptKey();
		if (!$key && !$_key) {
			throw new \Soter_Exception_500('encrypt key can not empty or you can set it in index.php : ->setEncryptKey()');
		}
		return substr(md5($_key . $attachKey), 0, 8);
	}
	static function encrypt($str, $key = '', $attachKey = '') {
		if (!$str) {
			return '';
		}
		$str = $str . '';
		$key = self::getEncryptKey($key, $attachKey);
		$block = mcrypt_get_block_size('des', 'ecb');
		$pad = $block - (strlen($str) % $block);
		$str .= str_repeat(chr($pad), $pad);
		return bin2hex(mcrypt_encrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB));
	}
	static function decrypt($str, $key = '', $attachKey = '') {
		if (!$str) {
			return '';
		}
		$str = $str . '';
		$key = self::getEncryptKey($key, $attachKey);
		$str = @pack("H*", $str);
		if (!$str) {
			return '';
		}
		$str = @mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
		$pad = ord($str[($len = strlen($str)) - 1]);
		return substr($str, 0, strlen($str) - $pad);
	}
	static function classIsExists($class) {
		if (class_exists($class, false)) {
			return true;
		}
		$classNamePath = str_replace('_', '/', $class);
		foreach (self::config()->getPackages() as $path) {
			if (file_exists($filePath = $path . self::config()->getClassesDirName() . '/' . $classNamePath . '.php')) {
				return true;
			}
		}
		return false;
	}
	/**
	 * 判断是否是ajax请求，只对jquery的ajax请求有效
	 * @return boolean
	 */
	static function isAjax() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}
	/**
	 * 获取系统临时目录路径
	 * @return type
	 */
	public function getTempPath() {
		$path = '';
		if (!function_exists('sys_get_temp_dir')) {
			if (!empty($_ENV['TMP'])) {
				$path = realpath($_ENV['TMP']);
			} elseif (!empty($_ENV['TMPDIR'])) {
				$path = realpath($_ENV['TMPDIR']);
			} elseif (!empty($_ENV['TEMP'])) {
				$path = realpath($_ENV['TEMP']);
			} else {
				$tempfile = tempnam(uniqid(rand(), TRUE), '');
				if (file_exists($tempfile)) {
					unlink($tempfile);
					$path = realpath(dirname($tempfile));
				}
			}
		} else {
			$path = sys_get_temp_dir();
		}
		return $path ? $path . '/' : '';
	}
}

/**
 * SoterPDO is simple and smart wrapper for PDO
 */
class Soter_PDO extends PDO {
	protected $transactionCounter = 0;
	private $isLast;
	public function isInTransaction() {
		return !$this->isLast;
	}
	public function beginTransaction() {
		if (!$this->transactionCounter++) {
			return parent::beginTransaction();
		}
		$this->exec('SAVEPOINT trans' . $this->transactionCounter);
		return $this->transactionCounter >= 0;
	}
	public function commit() {
		if (!--$this->transactionCounter) {
			$this->isLast = true;
			return parent::commit();
		}
		$this->isLast = false;
		return $this->transactionCounter >= 0;
	}
	public function rollback() {
		if (--$this->transactionCounter) {
			$this->exec('ROLLBACK TO trans' . $this->transactionCounter + 1);
			return true;
		}
		return parent::rollback();
	}
}
abstract class Soter_Database {
	private $driverType,
		$database,
		$tablePrefix,
		$pconnect,
		$debug,
		$charset,
		$collate,
		$tablePrefixSqlIdentifier,
		$slowQueryTime,
		$slowQueryHandle,
		$slowQueryDebug,
		$minIndexType,
		$indexDebug,
		$indexHandle,
		$masters,
		$slaves,
		$connectionMasters,
		$connectionSlaves,
		$versionThan56 = false,
		$_errorMsg,
		$_lastSql,
		$_lastPdoInstance,
		$_isInTransaction = false,
		$_config,
		$_lastInsertId = 0,
		$_cacheTime = 0,
		$_cacheKey,
		$_masterPdo = null,
		$_locked = false
	;
	public function __construct(Array $config = array()) {
		$this->setConfig($config);
	}
	public function &getLastPdoInstance() {
		return $this->_lastPdoInstance;
	}
	/**
	 * 锁定数据库连接，后面的读写都使用同一个主数据库连接
	 */
	public function lock() {
		$this->_locked = true;
		return $this;
	}
	/**
	 * 解锁数据库连接，后面的读写使用不同的数据库连接
	 */
	public function unlock() {
		$this->_locked = false;
		return $this;
	}
	/**
	 * 数据库连接是否处于锁定状态
	 * @return bool
	 */
	public function isLocked() {
		return $this->_locked;
	}
	public function lastId() {
		if (strtolower($this->getDriverType()) == 'sqlite') {
			//sqlite3的insertBatch是模拟的，
			//返回的最后插入id是这个批次最后一条记录的id，
			//而不是这个批次第一条记录的id，应该是这个批次第一条记录的id
			//这里通过计算得到这个批次第一条记录的id
			return $this->_lastInsertBatchCount > 1 ? ($this->_lastInsertId - $this->_lastInsertBatchCount + 1) : $this->_lastInsertId;
		} else {
			return $this->_lastInsertId;
		}
	}
	public function error() {
		return $this->_errorMsg;
	}
	public function close() {
		$this->_masterPdo = null;
		$this->_lastPdoInstance = null;
		$this->connectionMasters = array();
		$this->connectionSlaves = array();
		return $this;
	}
	public function lastSql() {
		return $this->_lastSql;
	}
	public function getSlowQueryDebug() {
		return $this->slowQueryDebug;
	}
	public function getMinIndexType() {
		return $this->minIndexType;
	}
	public function getIndexDebug() {
		return $this->indexDebug;
	}
	public function setSlowQueryDebug($slowQueryDebug) {
		$this->slowQueryDebug = $slowQueryDebug;
		return $this;
	}
	public function setMinIndexType($minIndexType) {
		$this->minIndexType = $minIndexType;
		return $this;
	}
	public function setIndexDebug($indexDebug) {
		$this->indexDebug = $indexDebug;
		return $this;
	}
	public function getSlowQueryTime() {
		return $this->slowQueryTime;
	}
	public function &getSlowQueryHandle() {
		return $this->slowQueryHandle;
	}
	public function &getIndexHandle() {
		return $this->indexHandle;
	}
	public function setSlowQueryTime($slowQueryTime) {
		$this->slowQueryTime = $slowQueryTime;
		return $this;
	}
	public function setSlowQueryHandle(Soter_Database_SlowQuery_Handle $slowQueryHandle) {
		$this->slowQueryHandle = $slowQueryHandle;
		return $this;
	}
	public function setIndexHandle(Soter_Database_Index_Handle $indexHandle) {
		$this->indexHandle = $indexHandle;
		return $this;
	}
	public function getConfig() {
		return $this->_config;
	}
	public function setConfig(Array $config = array()) {
		foreach (($this->_config = array_merge($this->getDefaultConfig(), $config)) as $key => $value) {
			$this->{$key} = $value;
		}
		$this->connectionMasters = array();
		$this->connectionSlaves = array();
		$this->_errorMsg = '';
		$this->_lastSql = '';
		$this->_isInTransaction = false;
		$this->_lastInsertId = 0;
		$this->_lastPdoInstance = NULL;
		$this->_cacheKey = '';
		$this->_cacheTime = 0;
		$this->_masterPdo = '';
		$this->_locked = false;
	}
	public function getDriverType() {
		return $this->driverType;
	}
	public function getMasters() {
		return $this->masters;
	}
	public function getMaster($key) {
		return $this->masters[$key];
	}
	public function getSlaves() {
		return $this->slaves;
	}
	public function getSlave($key) {
		return $this->slaves[$key];
	}
	public function getDatabase() {
		return $this->database;
	}
	public function getTablePrefix() {
		return $this->tablePrefix;
	}
	public function getPconnect() {
		return $this->pconnect;
	}
	public function getDebug() {
		return $this->debug;
	}
	public function getCharset() {
		return $this->charset;
	}
	public function getCollate() {
		return $this->collate;
	}
	public function getTablePrefixSqlIdentifier() {
		return $this->tablePrefixSqlIdentifier;
	}
	public function setDriverType($driverType) {
		$this->driverType = $driverType;
		return $this;
	}
	public function setMasters($masters) {
		$this->masters = $masters;
		return $this;
	}
	public function setSlaves($slaves) {
		$this->slaves = $slaves;
		return $this;
	}
	public function setDatabase($database) {
		$this->database = $database;
		return $this;
	}
	public function setTablePrefix($tablePrefix) {
		$this->tablePrefix = $tablePrefix;
		return $this;
	}
	public function setPconnect($pconnect) {
		$this->pconnect = $pconnect;
		return $this;
	}
	public function setDebug($debug) {
		$this->debug = $debug;
		return $this;
	}
	public function setCharset($charset) {
		$this->charset = $charset;
		return $this;
	}
	public function setCollate($collate) {
		$this->collate = $collate;
		return $this;
	}
	public function setTablePrefixSqlIdentifier($tablePrefixSqlIdentifier) {
		$this->tablePrefixSqlIdentifier = $tablePrefixSqlIdentifier;
		return $this;
	}
	public static function getDefaultConfig() {
		return array(
		    'driverType' => 'mysql',
		    'debug' => true,
		    'pconnect' => false,
		    'charset' => 'utf8',
		    'collate' => 'utf8_general_ci',
		    'database' => '',
		    'tablePrefix' => '',
		    'tablePrefixSqlIdentifier' => '_prefix_',
		    //是否记录慢查询
		    'slowQueryDebug' => false,
		    'slowQueryTime' => 3000, //慢查询最小时间，单位毫秒，1秒=1000毫秒
		    'slowQueryHandle' => null,
		    //是否记录没有满足设置的索引类型的查询
		    'indexDebug' => false,
		    /**
		     * 索引使用的最小情况，只有小于最小情况的时候才会记录sql到日志
		     * minIndexType值从好到坏依次是:
		     * system > const > eq_ref > ref > fulltext > ref_or_null
		     * > index_merge > unique_subquery > index_subquery > range
		     * > index > ALL一般来说，得保证查询至少达到range级别，最好能达到ref
		     */
		    'minIndexType' => 'ALL',
		    'indexHandle' => null,
		    'masters' => array(
			'master01' => array(
			    'hostname' => '127.0.0.1',
			    'port' => 3306,
			    'username' => 'root',
			    'password' => '',
			)
		    ),
		    'slaves' => array()
		);
	}
	private function _isSqlite() {
		return strtolower($this->getDriverType()) == 'sqlite';
	}
	private function _isMysql() {
		return strtolower($this->getDriverType()) == 'mysql';
	}
	private function _init() {
		$info = array(
		    'master' => array(
			'getMasters',
			'connectionMasters',
		    ),
		    'slave' => array(
			'getSlaves',
			'connectionSlaves',
		    ),
		);
		try {
			foreach ($info as $type => $group) {
				$configGroup = $this->{$group[0]}();
				$connections = &$this->{$group[1]};
				foreach ($configGroup as $key => $config) {
					if (!\Sr::arrayKeyExists($key, $connections)) {
						$options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;
						$options[\PDO::ATTR_PERSISTENT] = $this->getPconnect();
						if ($this->_isMysql()) {
							$options[\PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $this->getCharset() . ' COLLATE ' . $this->getCollate();
							$options[\PDO::ATTR_EMULATE_PREPARES] = TRUE; //empty($slaves) && (count($masters) == 1);
							$dsn = 'mysql:host=' . $config['hostname'] . ';port=' . $config['port'] . ';dbname=' . $this->getDatabase() . ';charset=' . $this->getCharset();
							$connections[$key] = new \Soter_PDO($dsn, $config['username'], $config['password'], $options);
							$connections[$key]->exec('SET NAMES ' . $this->getCharset());
						} elseif ($this->_isSqlite()) {
							if (!file_exists($this->getDatabase())) {
								throw new \Soter_Exception_Database('sqlite3 database file [' . \Sr::realPath($this->getDatabase()) . '] not found');
							}
							$connections[$key] = new \Soter_PDO('sqlite:' . $this->getDatabase(), null, null, $options);
						} else {
							throw new \Soter_Exception_Database('unknown driverType [ ' . $this->getDriverType() . ' ]');
						}
					}
				}
			}
			if (empty($this->connectionSlaves) && !empty($this->connectionMasters)) {
				$this->connectionSlaves[0] = $this->connectionMasters[array_rand($this->connectionMasters)];
			}
			if (empty($this->_masterPdo) && !empty($this->connectionMasters)) {
				$this->_masterPdo = $this->connectionMasters[array_rand($this->connectionMasters)];
			}
			return !(empty($this->connectionMasters) && empty($this->connectionSlaves));
		} catch (Exception $e) {
			$this->_displayError($e);
		}
	}
	public function begin() {
		if (!$this->_init()) {
			return FALSE;
		}
		$this->_masterPdo->beginTransaction();
		$this->_isInTransaction = TRUE;
	}
	public function commit() {
		if (!$this->_init()) {
			return FALSE;
		}
		$this->_masterPdo->commit();
		$this->_isInTransaction = $this->_masterPdo->isInTransaction();
	}
	public function rollback() {
		if (!$this->_init()) {
			return FALSE;
		}
		$this->_masterPdo->rollback();
	}
	public function cache($cacheTime, $cacheKey = '') {
		$this->_cacheTime = (int) $cacheTime;
		$this->_cacheKey = $cacheKey;
		return $this;
	}
	private function _checkPrefixIdentifier($str) {
		$prefix = $this->getTablePrefix();
		$identifier = $this->getTablePrefixSqlIdentifier();
		return $identifier ? str_replace($identifier, $prefix, $str) : $str;
	}
	/**
	 * 执行一个sql语句，写入型的返回bool或者影响的行数（insert,delete,replace,update），搜索型的返回结果集
	 * @param type $sql       sql语句
	 * @param array $values   参数
	 * @return boolean|\Soter_Database_Resultset
	 */
	public function execute($sql = '', array $values = array()) {
		if (!$this->_init()) {
			return FALSE;
		}
		$startTime = \Sr::microtime();
		$sql = $sql ? $this->_checkPrefixIdentifier($sql) : $this->getSql();
		$this->_lastSql = $sql;
		$values = !empty($values) ? $values : $this->_getValues();
		//读查询缓存
		$cacheHandle = null;
		$cacheKey = '';
		if ($this->_cacheTime) {
			$cacheKey = empty($this->_cacheKey) ? md5($sql . var_export($values, true)) : $this->_cacheKey;
			$cacheHandle = \Sr::config()->getCacheHandle();
			if (empty($cacheHandle)) {
				throw new \Soter_Exception_500('no cache handle found , please set cache handle');
			}
			$return = $cacheHandle->get($cacheKey);
			if (!is_null($return)) {
				$this->_cacheKey = '';
				$this->_cacheTime = 0;
				$this->_reset();
				return $return;
			}
		}
		$isWriteType = $this->_isWriteType($sql);
		$isWritetRowsType = $this->_isWriteRowsType($sql);
		$isWriteInsertType = $this->_isWriteInsertType($sql);
		$return = false;
		try {
			if ($this->_isInTransaction) {
				//事务模式
				$pdo = &$this->_masterPdo; //使用一个固定的随机的主数据库，init方法里面被初始化一次
				$this->_lastPdoInstance = &$pdo;
				if ($sth = $pdo->prepare($sql)) {
					if ($isWriteType) {
						$status = $sth->execute($values);
						$return = $isWritetRowsType ? $sth->rowCount() : $status;
						$this->_lastInsertId = $isWriteInsertType ? $pdo->lastInsertId() : 0;
					} else {
						$return = $sth->execute($values) ? $sth->fetchAll(\PDO::FETCH_ASSOC) : array();
						$return = new \Soter_Database_Resultset($return);
					}
				} else {
					$errorInfo = $pdo->errorInfo();
					$this->_displayError($errorInfo[2], $errorInfo[1]);
				}
			} else {
				//非事务模式
				if ($this->isLocked()) {
					//锁定状态使用固定的一个主数据库
					$pdo = &$this->_masterPdo;
				} else {
					//非锁定状态，使用随机选择一个主数据库进行写，随机选择一个从数据库进行读
					if ($isWriteType) {
						$pdo = &$this->connectionMasters[array_rand($this->connectionMasters)];
					} else {
						$pdo = &$this->connectionSlaves[array_rand($this->connectionSlaves)];
					}
				}
				$this->_lastPdoInstance = &$pdo;
				if ($sth = $pdo->prepare($sql)) {
					if ($isWriteType) {
						$status = $sth->execute($values);
						$return = $isWritetRowsType ? $sth->rowCount() : $status;
						$this->_lastInsertId = $isWriteInsertType ? $pdo->lastInsertId() : 0;
					} else {
						$return = $sth->execute($values) ? $sth->fetchAll(\PDO::FETCH_ASSOC) : array();
						$return = new \Soter_Database_Resultset($return);
					}
				} else {
					$errorInfo = $pdo->errorInfo();
					$this->_displayError($errorInfo[2], $errorInfo[1]);
				}
			}
			//查询消耗的时间
			$usingTime = (\Sr::microtime() - $startTime) . '';
			//explain查询
			$explainRows = array();
			if ($this->_isMysql() && ($this->slowQueryDebug || $this->indexDebug) && (($this->_isExplain56Type($sql) && $this->versionThan56) || ($this->_isExplainType($sql) && !$this->versionThan56))) {
				reset($this->connectionMasters);
				$sth = $this->connectionMasters[key($this->connectionMasters)]->prepare('EXPLAIN ' . $sql);
				$sth->execute($this->_getValues());
				$explainRows = $sth->fetchAll(\PDO::FETCH_ASSOC);
			}
			//慢查询记录
			if ($this->slowQueryDebug && ($usingTime >= $this->getSlowQueryTime())) {
				if ($this->slowQueryHandle instanceof Soter_Database_SlowQuery_Handle) {
					$this->slowQueryHandle->handle($sql, var_export($explainRows, true), $usingTime);
				}
			}
			//不满足索引条件的查询记录
			if ($this->indexDebug && $this->indexHandle instanceof Soter_Database_Index_Handle) {
				$badIndex = false;
				if ($this->_isMysql()) {
					$order = array(
					    'system' => 1, 'const' => 2, 'eq_ref' => 3, 'ref' => 4,
					    'fulltext' => 5, 'ref_or_null' => 6, 'index_merge' => 7, 'unique_subquery' => 8,
					    'index_subquery' => 9, 'range' => 10, 'index' => 11, 'all' => 12,
					);
					foreach ($explainRows as $row) {
						if (\Sr::arrayKeyExists(strtolower($row['type']), $order) && \Sr::arrayKeyExists(strtolower($this->getMinIndexType()), $order)) {
							$key = $order[strtolower($row['type'])];
							$minKey = $order[strtolower($this->getMinIndexType())];
							if ($key > $minKey) {
								if (stripos($row['Extra'], 'optimized') === false) {
									$badIndex = true;
									break;
								}
							}
						}
					}
				} elseif (strtolower($this->getDriverType()) == 'sqlite') {
					
				}
				if ($badIndex) {
					$this->indexHandle->handle($sql, var_export($explainRows, true), $usingTime);
				}
			}
		} catch (Exception $exc) {
			$this->_reset();
			$this->_displayError($exc);
		}
		//写查询缓存
		if ($this->_cacheTime) {
			$cacheHandle->set($cacheKey, $return, $this->_cacheTime);
		}
		$this->_cacheKey = '';
		$this->_cacheTime = 0;
		$this->_reset();
		return $return;
	}
	private function _isWriteType($sql) {
		if (!preg_match('/^\s*"?(SET|INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD DATA|COPY|ALTER|GRANT|REVOKE|LOCK|UNLOCK)\s+/i', $sql)) {
			return FALSE;
		}
		return TRUE;
	}
	private function _isWriteInsertType($sql) {
		if (!preg_match('/^\s*"?(INSERT|REPLACE)\s+/i', $sql)) {
			return FALSE;
		}
		return TRUE;
	}
	private function _isExplain56Type($sql) {
		if (!preg_match('/^\s*"?(SELECT|INSERT|UPDATE|DELETE|REPLACE)\s+/i', $sql)) {
			return FALSE;
		}
		return TRUE;
	}
	private function _isExplainType($sql) {
		if (!preg_match('/^\s*"?(SELECT)\s+/i', $sql)) {
			return FALSE;
		}
		return TRUE;
	}
	private function _isWriteRowsType($sql) {
		if (!preg_match('/^\s*"?(INSERT|UPDATE|DELETE|REPLACE)\s+/i', $sql)) {
			return FALSE;
		}
		return TRUE;
	}
	protected function _displayError($message, $code = 0) {
		$sql = $this->_lastSql ? ' , ' . "\n" . 'with query : ' . $this->_lastSql : '';
		$group = "Database Group : [ " . $this->group . " ] , error : ";
		if ($message instanceof Exception) {
			$this->_errorMsg = $message->getMessage() . $sql;
		} else {
			$this->_errorMsg = $message . $sql;
		}
		if ($this->getDebug() || $this->_isInTransaction) {
			if ($message instanceof Exception) {
				throw new \Soter_Exception_Database($group . $this->_errorMsg, 500, 'Soter_Exception_Database', $message->getFile(), $message->getLine());
			} else {
				throw new \Soter_Exception_Database($group . $message . $sql, $code);
			}
		}
	}
	public function getSqlValues() {
		return $this->_getValues();
	}
	public abstract function getSql();
	protected abstract function _getValues();
}
class Soter_Database_ActiveRecord extends Soter_Database {
	private $arSelect
		, $arFrom
		, $arJoin
		, $arWhere
		, $arGroupby
		, $arHaving
		, $arLimit
		, $arOrderby
		, $arSet
		, $arUpdateBatch
		, $arInsert
		, $arInsertBatch
		, $_asTable
		, $_asColumn
		, $_values
		, $_sqlType
		, $_currentSql
	;
	protected $_lastInsertBatchCount = 0
	;
	protected function _getValues() {
		return $this->_values;
	}
	public function __construct(Array $config = array()) {
		parent::__construct($config);
		$this->_reset();
	}
	protected function _reset() {
		$this->arSelect = array();
		$this->arFrom = array();
		$this->arJoin = array();
		$this->arWhere = array();
		$this->arGroupby = array();
		$this->arHaving = array();
		$this->arOrderby = array();
		$this->arLimit = '';
		$this->arSet = array();
		$this->arUpdateBatch = array();
		$this->arInsert = array();
		$this->arInsertBatch = array();
		$this->_asTable = array();
		$this->_asColumn = array();
		$this->_values = array();
		$this->_sqlType = 'select';
		$this->_currentSql = '';
	}
	public function select($select, $wrap = TRUE) {
		foreach (explode(',', $select) as $key) {
			$this->arSelect[] = array($key, $wrap);
		}
		return $this;
	}
	public function from($from, $as = '') {
		$this->arFrom = array($from, $as);
		if ($as) {
			$this->_asTable[$as] = 1;
		}
		return $this;
	}
	public function join($table, $on, $type = '') {
		$this->arJoin[] = array($table, $on, strtoupper($type));
		return $this;
	}
	public function where($where, $leftWrap = 'AND', $rightWrap = '') {
		if (!empty($where) && is_array($where)) {
			$this->arWhere[] = array($where, $leftWrap, $rightWrap, count($this->arWhere));
		}
		return $this;
	}
	public function groupBy($key) {
		$key = explode(',', $key);
		foreach ($key as $k) {
			$this->arGroupby[] = trim($k);
		}
		return $this;
	}
	public function having($having, $leftWrap = 'AND', $rightWrap = '') {
		$this->arHaving[] = array($having, $leftWrap, $rightWrap, count($this->arHaving));
		return $this;
	}
	public function orderBy($key, $type = 'desc') {
		$this->arOrderby[$key] = $type;
		return $this;
	}
	public function limit($offset, $count) {
		$this->arLimit = "$offset , $count";
		return $this;
	}
	public function insert($table, array $data) {
		$this->_sqlType = 'insert';
		$this->arInsert = $data;
		$this->_lastInsertBatchCount = 0;
		$this->from($table);
		return $this;
	}
	public function replace($table, array $data) {
		$this->_sqlType = 'replace';
		$this->arInsert = $data;
		$this->from($table);
		return $this;
	}
	private function _compileInsert() {
		$keys = array();
		$values = array();
		foreach ($this->arInsert as $key => $value) {
			$keys[] = $this->_protectIdentifier($key);
			$values[] = '?';
			$this->_values[] = $value;
		}
		if (!empty($keys)) {
			return '(' . implode(',', $keys) . ') ' . "\n" . 'VALUES (' . implode(',', $values) . ')';
		}
		return '';
	}
	public function insertBatch($table, array $data) {
		$this->_sqlType = 'insertBatch';
		$this->arInsertBatch = $data;
		$this->_lastInsertBatchCount = count($data);
		$this->from($table);
		return $this;
	}
	public function replaceBatch($table, array $data) {
		$this->_sqlType = 'replaceBatch';
		$this->arInsertBatch = $data;
		$this->_lastInsertBatchCount = count($data);
		$this->from($table);
		return $this;
	}
	private function _compileInsertBatch() {
		$keys = array();
		$values = array();
		if (!empty($this->arInsertBatch[0])) {
			foreach ($this->arInsertBatch[0] as $key => $value) {
				$keys[] = $this->_protectIdentifier($key);
			}
			foreach ($this->arInsertBatch as $row) {
				$_values = array();
				foreach ($row as $key => $value) {
					$_values[] = '?';
					$this->_values[] = $value;
				}
				$values[] = '(' . implode(',', $_values) . ')';
			}
			return '(' . implode(',', $keys) . ') ' . "\n VALUES " . implode(' , ', $values);
		}
		return '';
	}
	public function delete($table, array $where = array()) {
		$this->from($table);
		$this->where($where);
		$this->_sqlType = 'delete';
		return $this;
	}
	public function update($table, array $data = array(), array $where = array()) {
		$this->from($table);
		$this->where($where);
		foreach ($data as $key => $value) {
			if (is_bool($value)) {
				$this->set($key, (($value === FALSE) ? 0 : 1), true);
			} elseif (is_null($value)) {
				$this->set($key, 'NULL', false);
			} else {
				$this->set($key, $value, true);
			}
		}
		return $this;
	}
	/**
	 * 批量更新
	 *
	 * @param array $values 必须包含$index字段
	 * @param string $index  唯一字段名称，一般是主键id
	 * @return int
	 */
	public function updateBatch($table, array $values, $index) {
		$this->from($table);
		$this->_sqlType = 'updateBatch';
		$this->arUpdateBatch = array($values, $index);
		if (!empty($values[0])) {
			foreach ($values as $val) {
				$ids[] = $val[$index];
			}
			$this->where(array($index => $ids));
		}
		return $this;
	}
	private function _compileUpdateBatch() {
		list($values, $index) = $this->arUpdateBatch;
		if (count($values) && \Sr::arrayKeyExists("0.$index", $values)) {
			$ids = array();
			$final = array();
			$_values = array();
			foreach ($values as $key => $val) {
				$ids[] = $val[$index];
				foreach (array_keys($val) as $field) {
					if ($field != $index) {
						if (is_array($val[$field])) {
							$_column = explode(' ', key($val[$field]));
							$column = $this->_protectIdentifier($_column[0]);
							$op = isset($_column[1]) ? $_column[1] : '';
							$final[$field][] = 'WHEN ' . $this->_protectIdentifier($index) . ' = ? THEN ' . $column . ' ' . $op . ' ' . "?";
							$_values[$field][] =$val[$index] ;
							$_values[$field][] = current($val[$field]);
							
						} else {
							$final[$field][] = 'WHEN ' . $this->_protectIdentifier($index) . ' = ? THEN ' . "?";
							$_values[$field][] = $val[$index];
							$_values[$field][] = $val[$field];
						}
					}
				}
			}
			foreach ($_values as $field => $value) {
				if ($field == $index) {
					continue;
				}
				if (!empty($_values[$field]) && is_array($_values[$field])) {
					foreach ($value as $v) {
						$this->_values[] = $v;
					}
				}
			}
			$_values = null;
			$sql = "";
			$cases = '';
			foreach ($final as $k => $v) {
				$cases .= $this->_protectIdentifier($k) . ' = CASE ' . "\n";
				foreach ($v as $row) {
					$cases .= $row . "\n";
				}
				$cases .= 'ELSE ' . $this->_protectIdentifier($k) . ' END, ';
			}
			$sql .= substr($cases, 0, -2);
			return $sql;
		}
		return '';
	}
	public function set($key, $value, $wrap = true) {
		$this->_sqlType = 'update';
		$this->arSet[$key] = array($value, $wrap);
		return $this;
	}
	/**
	 * 加表前缀，保护字段名和表名
	 * @param String $str 比如：user.id , id
	 * @return String
	 */
	public function wrap($str) {
		$_key = explode('.', $str);
		if (count($_key) == 2) {
			return $this->_protectIdentifier($this->_checkPrefix($_key[0])) . '.' . $this->_protectIdentifier($_key[1]);
		} else {
			return $this->_protectIdentifier($_key[0]);
		}
	}
	public function getSql() {
		//在没有execute之前，防止多次调用导致values重复添加，这里在execute之前只编译一次，以后直接返回
		//execute之后$this->_currentSql会被_reset为空
		if ($this->_currentSql) {
			return $this->_currentSql;
		}
		switch ($this->_sqlType) {
			case 'select':
				$this->_currentSql = $this->_getSelectSql();
				break;
			case 'update':
				$this->_currentSql = $this->_getUpdateSql();
				break;
			case 'updateBatch':
				$this->_currentSql = $this->_getUpdateBatchSql();
				break;
			case 'insert':
				$this->_currentSql = $this->_getInsertSql();
				break;
			case 'insertBatch':
				$this->_currentSql = $this->_getInsertBatchSql();
				break;
			case 'replace':
				$this->_currentSql = $this->_getReplaceSql();
				break;
			case 'replaceBatch':
				$this->_currentSql = $this->_getReplaceBatchSql();
				break;
			case 'delete':
				$this->_currentSql = $this->_getDeleteSql();
				break;
		}
		return $this->_currentSql;
	}
	private function _getUpdateSql() {
		$sql[] = "\n" . 'UPDATE ';
		$sql[] = $this->_getFrom();
		$sql[] = "\n" . 'SET';
		$sql[] = $this->_compileSet();
		$sql[] = $this->_getWhere();
		$sql[] = $this->_getLimit();
		return implode(' ', $sql);
	}
	private function _getUpdateBatchSql() {
		$sql[] = "\n" . 'UPDATE ';
		$sql[] = $this->_getFrom();
		$sql[] = "\n" . 'SET';
		$sql[] = $this->_compileUpdateBatch();
		$sql[] = $this->_getWhere();
		return implode(' ', $sql);
	}
	private function _getInsertSql() {
		$sql[] = "\n" . 'INSERT INTO ';
		$sql[] = $this->_getFrom();
		$sql[] = $this->_compileInsert();
		return implode(' ', $sql);
	}
	private function _getInsertBatchSql() {
		$sql[] = "\n" . 'INSERT INTO ';
		$sql[] = $this->_getFrom();
		$sql[] = $this->_compileInsertBatch();
		return implode(' ', $sql);
	}
	private function _getReplaceSql() {
		$sql[] = "\n" . 'REPLACE INTO ';
		$sql[] = $this->_getFrom();
		$sql[] = $this->_compileInsert();
		return implode(' ', $sql);
	}
	private function _getReplaceBatchSql() {
		$sql[] = "\n" . 'REPLACE INTO ';
		$sql[] = $this->_getFrom();
		$sql[] = $this->_compileInsertBatch();
		return implode(' ', $sql);
	}
	private function _getDeleteSql() {
		$sql[] = "\n" . 'DELETE FROM ';
		$sql[] = $this->_getFrom();
		$sql[] = $this->_getWhere();
		return implode(' ', $sql);
	}
	private function _getSelectSql() {
		$from = $this->_getFrom();
		$where = $this->_getWhere();
		$having = '';
		foreach ($this->arHaving as $w) {
			$having .= call_user_func_array(array($this, '_compileWhere'), $w);
		}
		$having = trim($having);
		if ($having) {
			$having = "\n" . ' HAVING ' . $having;
		}
		$groupBy = trim($this->_compileGroupBy());
		if ($groupBy) {
			$groupBy = "\n" . ' GROUP BY ' . $groupBy;
		}
		$orderBy = trim($this->_compileOrderBy());
		if ($orderBy) {
			$orderBy = "\n" . ' ORDER BY ' . $orderBy;
		}
		$limit = $this->_getLimit();
		$select = $this->_compileSelect();
		$sql = "\n" . ' SELECT ' . $select
			. "\n" . ' FROM ' . $from
			. $where
			. $groupBy
			. $having
			. $orderBy
			. $limit
		;
		return $sql;
	}
	private function _compileSet() {
		$set = array();
		foreach ($this->arSet as $key => $value) {
			list($value, $wrap) = $value;
			if ($wrap) {
				$set[] = $this->_protectIdentifier($key) . ' = ' . '?';
				$this->_values[] = $value;
			} else {
				$set[] = $this->_protectIdentifier($key) . ' = ' . $value;
			}
		}
		return implode(' , ', $set);
	}
	private function _compileGroupBy() {
		$groupBy = array();
		foreach ($this->arGroupby as $key) {
			$_key = explode('.', $key);
			if (count($_key) == 2) {
				$groupBy[] = $this->_protectIdentifier($this->_checkPrefix($_key[0])) . '.' . $this->_protectIdentifier($_key[1]);
			} else {
				$groupBy[] = $this->_protectIdentifier($_key[0]);
			}
		}
		return implode(' , ', $groupBy);
	}
	private function _compileOrderBy() {
		$orderby = array();
		foreach ($this->arOrderby as $key => $type) {
			$type = strtoupper($type);
			$_key = explode('.', $key);
			if (count($_key) == 2) {
				$orderby[] = $this->_protectIdentifier($this->_checkPrefix($_key[0])) . '.' . $this->_protectIdentifier($_key[1]) . ' ' . $type;
			} else {
				$orderby[] = $this->_protectIdentifier($_key[0]) . ' ' . $type;
			}
		}
		return implode(' , ', $orderby);
	}
	private function _compileWhere($where, $leftWrap = 'AND', $rightWrap = '', $index = -1) {
		$_where = array();
		if ($index == 0) {
			$str = strtoupper(trim($leftWrap));
			foreach (array('AND', 'OR') as $v) {
				if (stripos($str, $v) !== false) {
					$leftWrap = '';
					break;
				}
			}
		}
		if (is_string($where)) {
			return ' ' . $leftWrap . ' ' . $where . $rightWrap . ' ';
		}
		foreach ($where as $key => $value) {
			$key = trim($key);
			$_key = explode(' ', $key, 2);
			$op = count($_key) == 2 ? $_key[1] : '';
			$key = explode('.', $_key[0]);
			if (count($key) == 2) {
				$key = $this->_protectIdentifier($this->_checkPrefix($key[0])) . '.' . $this->_protectIdentifier($key[1]);
			} else {
				$key = $this->_protectIdentifier(current($key));
			}
			if (is_array($value)) {
				$op = $op ? $op . ' IN ' : ' IN ';
				$op = strtoupper($op);
				$_where[] = $key . ' ' . $op . '(' . implode(',', array_fill(0, count($value), '?')) . ')';
				foreach ($value as $v) {
					array_push($this->_values, $v);
				}
			} elseif (is_bool($value)) {
				$op = $op ? $op : '=';
				$op = strtoupper($op);
				$value = $value ? 1 : 0;
				$_where[] = $key . ' ' . $op . ' ? ';
				array_push($this->_values, $value);
			} elseif (is_null($value)) {
				$op = $op ? $op : 'IS';
				$op = strtoupper($op);
				$_where[] = $key . ' ' . $op . ' NULL ';
			} else {
				$op = $op ? $op : '=';
				$op = strtoupper($op);
				$_where[] = $key . ' ' . $op . ' ? ';
				array_push($this->_values, $value);
			}
		}
		return ' ' . $leftWrap . ' ' . implode(' AND ', $_where) . $rightWrap . ' ';
	}
	private function _compileSelect() {
		$selects = $this->arSelect;
		if (empty($selects)) {
			$selects[] = array('*', true);
		}
		foreach ($selects as $key => $_value) {
			$protect = $_value[1];
			$value = trim($_value[0]);
			if ($value != '*') {
				$_info = explode('.', $value);
				if (count($_info) == 2) {
					$_v = $this->_checkPrefix($_info[0]);
					$_info[0] = $protect ? $this->_protectIdentifier($_v) : $_v;
					$_info[1] = $protect ? $this->_protectIdentifier($_info[1]) : $_info[1];
					$value = implode('.', $_info);
				} else {
					$value = $protect ? $this->_protectIdentifier($value) : $value;
				}
			}
			$selects[$key] = $value;
		}
		return implode(',', $selects);
	}
	private function _compileFrom($from, $as = '') {
		if ($as) {
			$this->_asTable[$as] = 1;
			$as = ' AS ' . $this->_protectIdentifier($as) . ' ';
		}
		return $this->_protectIdentifier($this->_checkPrefix($from)) . $as;
	}
	private function _compileJoin($table, $on, $type = '') {
		if (is_array($table)) {
			$this->_asTable[current($table)] = 1;
			$table = $this->_protectIdentifier($this->_checkPrefix(key($table))) . ' AS ' . $this->_protectIdentifier(current($table)) . ' ';
		} else {
			$table = $this->_protectIdentifier($this->_checkPrefix($table));
		}
		list($left, $right) = explode('=', $on);
		$_left = explode('.', $left);
		$_right = explode('.', $right);
		if (count($_left) == 2) {
			$_left[0] = $this->_protectIdentifier($this->_checkPrefix($_left[0]));
			$_left[1] = $this->_protectIdentifier($_left[1]);
			$left = ' ' . implode('.', $_left) . ' ';
		} else {
			$left = $this->_protectIdentifier($left);
		}
		if (count($_right) == 2) {
			$_right[0] = $this->_protectIdentifier($this->_checkPrefix($_right[0]));
			$_right[1] = $this->_protectIdentifier($_right[1]);
			$right = ' ' . implode('.', $_right) . ' ';
		} else {
			$right = $this->_protectIdentifier($right);
		}
		$on = $left . ' = ' . $right;
		return ' ' . $type . ' JOIN ' . $table . ' ON ' . $on . ' ';
	}
	private function _checkPrefix($str) {
		if (stripos($str, '(') || stripos($str, ')') || trim($str) == '*') {
			return $str;
		}
		$prefix = $this->getTablePrefix();
		if ($prefix && strpos($str, $prefix) === FALSE) {
			if (!\Sr::arrayKeyExists($str, $this->_asTable)) {
				return $prefix . $str;
			}
		}
		return $str;
	}
	private function _protectIdentifier($str) {
		if (stripos($str, '(') || stripos($str, ')') || trim($str) == '*') {
			return $str;
		}
		$_str = explode(' ', $str);
		if (count($_str) == 3 && strtolower($_str[1]) == 'as') {
			return "`{$_str[0]}` AS `{$_str[2]}`";
		} else {
			return "`$str`";
		}
	}
	private function _getFrom() {
		$table = ' ' . call_user_func_array(array($this, '_compileFrom'), $this->arFrom) . ' ';
		foreach ($this->arJoin as $join) {
			$table .= call_user_func_array(array($this, '_compileJoin'), $join);
		}
		return $table;
	}
	private function _getWhere() {
		$where = '';
		//如果where中存在空的in，说明搜索条件一定是假，那么用0代表where假条件
		$hasEmptyIn = false;
		foreach ($this->arWhere as $w) {
			foreach ($w[0] as $value) {
				if (is_array($value) && empty($value)) {
					$hasEmptyIn = true;
					break;
				}
			}
			if ($hasEmptyIn) {
				break;
			}
			$where .= call_user_func_array(array($this, '_compileWhere'), $w);
		}
		if ($hasEmptyIn) {
			return ' WHERE 0';
		}
		$where = trim($where);
		if ($where) {
			$where = "\n" . ' WHERE ' . $where;
		}
		return $where;
	}
	private function _getLimit() {
		$limit = $this->arLimit;
		if ($limit) {
			$limit = "\n" . ' LIMIT ' . $limit;
		}
		return $limit;
	}
	public function __toString() {
		return $this->getSql();
	}
}
class Soter_Database_Resultset {
	private $_resultSet = array(),
		$_rowsKey = ''
	;
	public function __construct($resultSet) {
		$this->_resultSet = $resultSet;
	}
	public function total() {
		return count($this->_resultSet);
	}
	public function rows($isAssoc = true) {
		$key = $this->_rowsKey;
		$this->_rowsKey = '';
		if ($key) {
			if ($isAssoc) {
				$rows = array();
				foreach ($this->_resultSet as $row) {
					$rows[$row[$key]] = $row;
				}
				return $rows;
			} else {
				$rows = array();
				foreach ($this->_resultSet as $row) {
					$rows[$row[$key]] = array_values($row);
				}
				return $rows;
			}
		} else {
			if ($isAssoc) {
				return $this->_resultSet;
			} else {
				$rows = array();
				foreach ($this->_resultSet as $row) {
					$rows[] = array_values($row);
				}
				return $rows;
			}
		}
	}
	public function row($index = null, $isAssoc = true) {
		if (!is_null($index) && \Sr::arrayKeyExists($index, $this->_resultSet)) {
			return $isAssoc ? $this->_resultSet[$index] : array_values($this->_resultSet[$index]);
		} else {
			$row = current($this->_resultSet);
			return $isAssoc ? (is_array($row) ? $row : array()) : array_values($row);
		}
	}
	public function object($beanClassName, $index = null) {
		$beanDirName = \Sr::config()->getBeanDirName();
		if (stripos($beanClassName, $beanDirName . '_') === false) {
			$beanClassName = $beanDirName . '_' . $beanClassName;
		}
		$object = new $beanClassName();
		if (!($object instanceof Soter_Bean)) {
			throw new \Soter_Exception_500('error class [ ' . $beanClassName . ' ] , need instanceof Soter_Bean');
		}
		$row = $this->row($index);
		foreach ($row as $key => $value) {
			$method = "set" . str_replace(' ', '', ucwords(str_replace('_', ' ', $key))) . "";
			$object->{$method}($value);
		}
		return $object;
	}
	public function objects($beanClassName) {
		$rowsKey = $this->_rowsKey;
		$this->_rowsKey = '';
		$beanDirName = \Sr::config()->getBeanDirName();
		if (stripos($beanClassName, $beanDirName . '_') === false) {
			$beanClassName = $beanDirName . '_' . $beanClassName;
		}
		$object = new $beanClassName();
		if (!($object instanceof Soter_Bean)) {
			throw new \Soter_Exception_500('error class [ ' . $beanClassName . ' ] , need instanceof Soter_Bean');
		}
		$objects = array();
		$rows = $this->rows();
		foreach ($rows as $row) {
			$object = new $beanClassName();
			foreach ($row as $key => $value) {
				$method = "set" . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
				$object->{$method}($value);
			}
			if ($rowsKey) {
				$objects[$row[$rowsKey]] = $object;
			} else {
				$objects[] = $object;
			}
		}
		return $objects;
	}
	public function values($columnName) {
		$columns = array();
		foreach ($this->_resultSet as $row) {
			if (\Sr::arrayKeyExists($columnName, $row)) {
				$columns[] = $row[$columnName];
			} else {
				return array();
			}
		}
		return $columns;
	}
	public function value($columnName, $default = null, $index = null) {
		$row = $this->row($index);
		return ($columnName && \Sr::arrayKeyExists($columnName, $row)) ? $row[$columnName] : $default;
	}
	public function key($columnName) {
		$this->_rowsKey = $columnName;
		return $this;
	}
}

interface Soter_Logger_Writer {
	public function write(Soter_Exception $exception);
}
interface Soter_Request {
	public function getPathInfo();
	public function getQueryString();
}
interface Soter_Uri_Rewriter {
	public function rewrite($uri);
}
interface Soter_Exception_Handle {
	public function handle(Soter_Exception $exception);
}
interface Soter_Maintain_Handle {
	public function handle();
}
interface Soter_Database_SlowQuery_Handle {
	public function handle($sql, $explainString, $time);
}
interface Soter_Database_Index_Handle {
	public function handle($sql, $explainString, $time);
}
interface Soter_Cache {
	public function set($key, $value, $cacheTime = 0);
	public function get($key);
	public function delete($key);
	public function clean();
	public function &instance($key = null, $isRead = true);
	public function reset();
}

abstract class Soter_Controller {
	
}
abstract class Soter_Model {
	
}
abstract class Soter_Dao {
	private $db;
	public function __construct() {
		$this->db = \Sr::db();
	}
	/**
	 * 设置Dao中使用的数据库操作对象
	 * @param Soter_Database_ActiveRecord $db
	 * @return \Soter_Dao
	 */
	public function setDb(Soter_Database_ActiveRecord $db) {
		$this->db = $db;
		return $this;
	}
	/**
	 * 获取Dao中使用的数据库操作对象
	 * @return Soter_Database_ActiveRecord
	 */
	public function &getDb() {
		return $this->db;
	}
	public abstract function getTable();
	public abstract function getPrimaryKey();
	public abstract function getColumns();
	/**
	 * 添加数据
	 * @param array $data  需要添加的数据
	 * @return int 最后插入的id，失败为0
	 */
	public function insert($data) {
		$num = $this->getDb()->insert($this->getTable(), $data)->execute();
		return $num ? $this->getDb()->lastId() : 0;
	}
	/**
	 * 批量添加数据
	 * @param array $rows  需要添加的数据
	 * @return int 插入的数据中第一条的id，失败为0
	 */
	public function insertBatch($rows) {
		$num = $this->getDb()->insertBatch($this->getTable(), $rows)->execute();
		return $num ? $this->getDb()->lastId() : 0;
	}
	/**
	 * 更新数据
	 * @param type $data  需要更新的数据
	 * @param type $where     可以是where条件关联数组，还可以是主键值。
	 * @return boolean
	 */
	public function update($data, $where) {
		$where = is_array($where) ? $where : array($this->getPrimaryKey() => $where);
		return $this->getDb()->where($where)->update($this->getTable(), $data)->execute();
	}
	/**
	 * 更新数据
	 * @param type $data  需要批量更新的数据
	 * @param type $index  需要批量更新的数据中的主键名称
	 * @return boolean
	 */
	public function updateBatch($data, $index) {
		return $this->getDb()->updateBatch($this->getTable(), $data, $index)->execute();
	}
	/**
	 * 获取一条或者多条数据
	 * @param type $values      可以是一个主键的值或者主键的值数组，还可以是where条件
	 * @param boolean $isRows  返回多行记录还是单行记录，true：多行，false：单行
	 * @param type $orderBy    当返回多行记录时，可以指定排序，
	 * 			     比如：array('time'=>'desc')或者array('time'=>'desc','id'=>'asc')
	 * @return int
	 */
	public function find($values, $isRows = false, Array $orderBy = array()) {
		if (empty($values)) {
			return 0;
		}
		if (is_array($values)) {
			$is_asso = array_diff_assoc(array_keys($values), range(0, sizeof($values))) ? TRUE : FALSE;
			if ($is_asso) {
				$this->getDb()->where($values);
			} else {
				$this->getDb()->where(array($this->getPrimaryKey() => array_values($values)));
			}
		} else {
			$this->getDb()->where(array($this->getPrimaryKey() => $values));
		}
		foreach ($orderBy as $k => $v) {
			$this->getDb()->orderBy($k, $v);
		}
		if (!$isRows) {
			$this->getDb()->limit(0, 1);
		}
		$rs = $this->getDb()->from($this->getTable())->execute();
		if ($isRows) {
			return $rs->rows();
		} else {
			return $rs->row();
		}
	}
	/**
	 * 获取所有数据
	 * @param type $where   where条件数组
	 * @param type $orderBy 排序，比如：array('time'=>'desc')或者array('time'=>'desc','id'=>'asc')
	 * @param type $limit   limit数量，比如：10
	 * @param type $fields  要搜索的字段，比如：id,name。留空默认*
	 * @return type
	 */
	public function findAll($where = null, Array $orderBy = array(), $limit = null, $fields = null) {
		if (!is_null($fields)) {
			$this->getDb()->select($fields);
		}
		if (!is_null($where)) {
			$this->getDb()->where($where);
		}
		foreach ($orderBy as $k => $v) {
			$this->getDb()->orderBy($k, $v);
		}
		if (!is_null($limit)) {
			$this->getDb()->limit(0, $limit);
		}
		return $this->getDb()->from($this->getTable())->execute()->rows();
	}
	/**
	 * 根据条件获取一个字段的值或者数组
	 * @param type $col         字段名称
	 * @param type $where       可以是一个主键的值或者主键的值数组，还可以是where条件
	 * @param boolean $isRows  返回多行记录还是单行记录，true：多行，false：单行
	 * @param type $orderBy    当返回多行记录时，可以指定排序，比如：array('time'=>'desc')或者array('time'=>'desc','id'=>'asc')
	 * @return type
	 */
	public function findCol($col, $where, $isRows = false, Array $orderBy = array()) {
		$row = $this->find($where, $isRows, $orderBy);
		if (!$isRows) {
			return isset($row[$col]) ? $row[$col] : null;
		} else {
			$vals = array();
			foreach ($row as $v) {
				$vals[] = $v[$col];
			}
			return $vals;
		}
	}
	/**
	 *
	 * 根据条件删除记录
	 * @param type $values 可以是一个主键的值或者主键主键的值数组
	 * @param type $cond   附加的where条件，关联数组
	 * 成功则返回影响的行数，失败返回false
	 */
	public function delete($values, $cond = NULL) {
		if (!empty($values)) {
			$this->getDb()->where(array($this->getPrimaryKey() => is_array($values) ? array_values($values) : $values));
		}
		if (!empty($cond)) {
			$this->getDb()->where($cond);
		}
		return $this->getDb()->delete($this->getTable())->execute();
	}
	/**
	 * 分页方法
	 * @param int $page       第几页
	 * @param int $pagesize   每页多少条
	 * @param string $url     基础url，里面的{page}会被替换为实际的页码
	 * @param string $fields  select的字段，全部用*，多个字段用逗号分隔
	 * @param array  $where    where条件，关联数组
	 * @param string $orderBy 排序字段，比如：array('time'=>'desc')或者array('time'=>'desc','id'=>'asc')
	 * @param array $pageBarOrder   分页条组成，可以参考手册分页条部分
	 * @param int   $pageBarACount 分页条a的数量，可以参考手册分页条部分
	 * @return type
	 */
	public function getPage($page, $pagesize, $url, $fields = '*', Array $where = null, Array $orderBy = array(), $pageBarOrder = array(1, 2, 3, 4, 5, 6), $pageBarACount = 10) {
		$data = array();
		if (is_array($where)) {
			$this->getDb()->where($where);
		}
		$total = $this->getDb()->select('count(*) as total')
			->from($this->getTable())
			->execute()
			->value('total');
		//这里必须重新附加条件，上面的count会重置条件
		if (is_array($where)) {
			$this->getDb()->where($where);
		}
		foreach ($orderBy as $k => $v) {
			$this->getDb()->orderBy($k, $v);
		}
		$data['items'] = $this->getDb()
				->select($fields)
				->limit(($page - 1) * $pagesize, $pagesize)
				->from($this->getTable())->execute()->rows();
		$data['page'] = \Sr::page($total, $page, $pagesize, $url, $pageBarOrder, $pageBarACount);
		return $data;
	}
	/**
	 * SQL搜索
	 * @param type $page      第几页
	 * @param type $pagesize  每页多少条
	 * @param type $url       基础url，里面的{page}会被替换为实际的页码
	 * @param type $fields    select的字段，全部用*，多个字段用逗号分隔
	 * @param type $cond      是条件字符串，SQL语句where后面的部分，不要带limit
	 * @param type $values    $cond中的问号的值数组，$cond中使用?可以防止sql注入
	 * @param array $pageBarOrder   分页条组成，可以参考手册分页条部分
	 * @param int   $pageBarACount 分页条a的数量，可以参考手册分页条部分
	 * @return type
	 */
	public function search($page, $pagesize, $url, $fields, $cond, Array $values = array(), $pageBarOrder = array(1, 2, 3, 4, 5, 6), $pageBarACount = 10) {
		$data = array();
		$table = $this->getDb()->getTablePrefix() . $this->getTable();
		$rs = $this->getDb()
			->execute('select count(*) as total from ' . $table . (strpos(trim($cond), 'order') === 0 ? ' ' : ' where ') . $cond, $values);
		//如果 $cond 包含 group by，结果条数是$rs->total()
		$total = $rs->total() > 1 ? $rs->total() : $rs->value('total');
		$data['items'] = $this->getDb()
			->execute('select ' . $fields . ' from ' . $table . (strpos(trim($cond), 'order') === 0 ? ' ' : ' where ') . $cond . ' limit ' . (($page - 1) * $pagesize) . ',' . $pagesize, $values)
			->rows();
		$data['page'] = \Sr::page($total, $page, $pagesize, $url, $pageBarOrder, $pageBarACount);
		return $data;
	}
}
abstract class Soter_Business {
	
}
abstract class Soter_Bean {
	
}
abstract class Soter_Task {
	protected $debug = false, $debugError = false;
	public function __construct() {
		if (!\Sr::isCli()) {
			throw new \Soter_Exception_500('Task only in cli mode');
		}
		if (!function_exists('shell_exec')) {
			throw new \Soter_Exception_500('Function [ shell_exec ] was disabled , run task must be enabled it .');
		}
	}
	public function _execute(Soter_CliArgs $args) {
		$this->debug = $args->get('debug');
		$this->debugError = $args->get('debug-error');
		$startTime = \Sr::microtime();
		$class = get_class($this);
		if ($this->debugError) {
			$_startTime = date('Y-m-d H:i:s.') . substr($startTime . '', strlen($startTime . '') - 3);
			$error = $this->execute($args);
			if ($error) {
				$this->_log('Task [ ' . $class . ' ] execute failed , started at [ ' . $_startTime . ' ], use time ' . (\Sr::microtime() - $startTime) . ' ms , exited with error : [ ' . $error . ' ]');
				$this->_log('', false);
			}
		} else {
			$this->_log('Task [ ' . $class . ' ] start');
			$this->execute($args);
			$this->_log('Task [ ' . $class . ' ] end , use time ' . (\Sr::microtime() - $startTime) . ' ms');
			$this->_log('', false);
		}
	}
	public function _log($msg, $time = true) {
		if ($this->debug || $this->debugError) {
			$nowTime = '' . \Sr::microtime();
			echo ($time ? date('[Y-m-d H:i:s.' . substr($nowTime, strlen($nowTime) - 3) . ']') . ' [PID:' . sprintf('%- 5d', getmypid()) . '] ' : '') . $msg . "\n";
		}
	}
	public final function pidIsExists($pid) {
		if (PATH_SEPARATOR == ':') {
			//linux
			return trim(shell_exec("ps ax | awk '{ print $1 }' | grep -e \"^{$pid}$\""), "\n") == $pid;
		} else {
			//windows
			return preg_match("/\t?\s?$pid\t?\s?/", shell_exec('tasklist /NH /FI "PID eq ' . $pid . '"'));
		}
	}
	abstract function execute(Soter_CliArgs $args);
}
abstract class Soter_Task_Single extends Soter_Task {
	public function _execute(Soter_CliArgs $args) {
		$this->debug = $args->get('debug');
		$class = get_class($this);
		$startTime = \Sr::microtime();
		$this->_log('Single Task [ ' . $class . ' ] start');
		$lockFilePath = $args->get('pid');
		if (!$lockFilePath) {
			$tempDirPath = \Sr::config()->getStorageDirPath();
			$key = md5(\Sr::config()->getApplicationDir() .
				\Sr::config()->getClassesDirName() . '/'
				. \Sr::config()->getTaskDirName() . '/'
				. str_replace('_', '/', get_class($this)) . '.php');
			$lockFilePath = \Sr::realPath($tempDirPath) . '/' . $key . '.pid';
		}
		if (file_exists($lockFilePath)) {
			$pid = file_get_contents($lockFilePath);
			//lockfile进程pid存在，直接返回
			if ($this->pidIsExists($pid)) {
				$this->_log('Single Task [ ' . $class . ' ] is running with pid ' . $pid . ' , now exiting...');
				$this->_log('Single Task [ ' . $class . ' ] end , use time ' . (\Sr::microtime() - $startTime) . ' ms');
				$this->_log('', false);
				return;
			}
		}
		//写入进程pid到lockfile
		if (file_put_contents($lockFilePath, getmypid()) === false) {
			throw new \Soter_Exception_500('can not create file : [ ' . $lockFilePath . ' ]');
		}
		$this->_log('update pid file [ ' . $lockFilePath . ' ]');
		$this->execute($args);
		@unlink($lockFilePath);
		$this->_log('clean pid file [ ' . $lockFilePath . ' ]');
		$this->_log('Single Task [ ' . $class . ' ] end , use time ' . (\Sr::microtime() - $startTime) . ' ms');
		$this->_log('', false);
	}
}
abstract class Soter_Task_Multiple extends Soter_Task {
	protected abstract function getMaxCount();
	public function _execute(Soter_CliArgs $args) {
		$this->debug = $args->get('debug');
		$class = get_class($this);
		$startTime = \Sr::microtime();
		$this->_log('Multiple Task [ ' . $class . ' ] start');
		$lockFilePath = $args->get('pid');
		if (!$lockFilePath) {
			$tempDirPath = \Sr::config()->getStorageDirPath();
			$key = md5(\Sr::config()->getApplicationDir() .
				\Sr::config()->getClassesDirName() . '/'
				. \Sr::config()->getTaskDirName() . '/'
				. str_replace('_', '/', get_class($this)) . '.php');
			$lockFilePath = \Sr::realPath($tempDirPath) . '/' . $key . '.pid';
		}
		$alivedPids = array();
		if (file_exists($lockFilePath)) {
			$count = 0;
			$pids = explode("\n", file_get_contents($lockFilePath));
			foreach ($pids as $pid) {
				if ($pid = (int) $pid) {
					if ($this->pidIsExists($pid)) {
						$alivedPids[] = $pid;
						if (++$count > $this->getMaxCount() - 1) {
							//进程数达到最大值，直接返回
							$this->_log('Multiple Task [ ' . $class . ' ] reach max count : ' . $this->getMaxCount() . ' , now exiting...');
							$this->_log('Multiple Task [ ' . $class . ' ] end , use time ' . (\Sr::microtime() - $startTime) . ' ms');
							$this->_log('', false);
							return;
						}
					}
				}
			}
		}
		$alivedPids[] = getmypid();
		//写入存活进程pid到lockfile
		if (file_put_contents($lockFilePath, implode("\n", $alivedPids)) === false) {
			throw new \Soter_Exception_500('can not create file : [ ' . $lockFilePath . ' ]');
		}
		$this->_log('update pid file [ ' . $lockFilePath . ' ]');
		$this->execute($args);
		$this->_log('clean pid file [ ' . $lockFilePath . ' ]');
		$this->_log('Multiple Task [ ' . $class . ' ] end , use time ' . (\Sr::microtime() - $startTime) . ' ms');
		$this->_log('', false);
	}
}
/**
 * @property Soter_Route $route
 */
abstract class Soter_Router {
	protected $route;
	public function __construct() {
		$this->route = new \Soter_Route();
	}
	/**
	 *
	 * @return \Soter_Route
	 */
	public abstract function find();
	public function &route() {
		return $this->route;
	}
}
abstract class Soter_Exception extends Exception {
	protected $errorMessage, $errorCode, $errorFile, $errorLine, $errorType, $trace,
		$httpStatusLine = 'HTTP/1.0 500 Internal Server Error',
		$exceptionName = 'Soter_Exception';
	public function __construct($errorMessage = '', $errorCode = 0, $errorType = 'Exception', $errorFile = '', $errorLine = '0') {
		parent::__construct($errorMessage, $errorCode);
		$this->errorMessage = $errorMessage;
		$this->errorCode = $errorCode;
		$this->errorType = $errorType;
		$this->errorFile = \Sr::realPath($errorFile);
		$this->errorLine = $errorLine;
		$this->trace = debug_backtrace(false);
	}
	public function errorType2string($errorType) {
		$value = $errorType;
		$levelNames = array(
		    E_ERROR => 'ERROR', E_WARNING => 'WARNING',
		    E_PARSE => 'PARSE', E_NOTICE => 'NOTICE',
		    E_CORE_ERROR => 'CORE_ERROR', E_CORE_WARNING => 'CORE_WARNING',
		    E_COMPILE_ERROR => 'COMPILE_ERROR', E_COMPILE_WARNING => 'COMPILE_WARNING',
		    E_USER_ERROR => 'USER_ERROR', E_USER_WARNING => 'USER_WARNING',
		    E_USER_NOTICE => 'USER_NOTICE');
		if (defined('E_STRICT')) {
			$levelNames[E_STRICT] = 'STRICT';
		}
		if (defined('E_DEPRECATED')) {
			$levelNames[E_DEPRECATED] = 'DEPRECATED';
		}
		if (defined('E_USER_DEPRECATED')) {
			$levelNames[E_USER_DEPRECATED] = 'USER_DEPRECATED';
		}
		if (defined('E_RECOVERABLE_ERROR')) {
			$levelNames[E_RECOVERABLE_ERROR] = 'RECOVERABLE_ERROR';
		}
		$levels = array();
		if (($value & E_ALL) == E_ALL) {
			$levels[] = 'E_ALL';
			$value&=~E_ALL;
		}
		foreach ($levelNames as $level => $name) {
			if (($value & $level) == $level) {
				$levels[] = $name;
			}
		}
		if (empty($levelNames[$this->errorCode])) {
			return $this->errorType ? $this->errorType : 'General Error';
		}
		return implode(' | ', $levels);
	}
	public function getErrorMessage() {
		return $this->errorMessage ? $this->errorMessage : $this->getMessage();
	}
	public function getErrorCode() {
		return $this->errorCode ? $this->errorCode : $this->getCode();
	}
	public function getEnvironment() {
		return \Sr::config()->getEnvironment();
	}
	public function getErrorFile($safePath = FALSE) {
		$file = $this->errorFile ? $this->errorFile : $this->getFile();
		return $safePath ? \Sr::safePath($file) : $file;
	}
	public function getErrorLine() {
		return $this->errorLine ? $this->errorLine : ( $this->errorFile ? $this->errorLine : $this->getLine());
	}
	public function getErrorType() {
		return $this->errorType2string($this->errorCode);
	}
	public function render($isJson = FALSE, $return = FALSE) {
		if ($isJson) {
			$string = $this->renderJson();
		} elseif (\Sr::isCli()) {
			$string = $this->renderCli();
		} else {
			$string = str_replace('</body>', $this->getTraceString(FALSE) . '</body>', $this->renderHtml());
		}
		if ($return) {
			return $string;
		} else {
			echo $string;
		}
	}
	public function getTraceCliString() {
		return $this->getTraceString(TRUE);
	}
	public function getTraceHtmlString() {
		return $this->getTraceString(FALSE);
	}
	private function getTraceString($isCli) {
		$trace = array_reverse($this->trace);
		$str = $isCli ? "[ Debug Backtrace ]\n" : '<div style="padding:10px;">[ Debug Backtrace ]<br/>';
		if (empty($trace)) {
			return '';
		}
		$i = 1;
		foreach ($trace as $e) {
			$file = \Sr::safePath(Sr::arrayGet($e, 'file'));
			$line = \Sr::arrayGet($e, 'line');
			$func = (!empty($e['class']) ? "{$e['class']}{$e['type']}{$e['function']}()" : "{$e['function']}()");
			$str.="&rarr; " . ($i++) . ".{$func} " . ($line ? "[ line:{$line} {$file} ]" : '') . ($isCli ? "\n" : '<br/>');
		}
		$str.=$isCli ? "\n" : '</div>';
		return $str;
	}
	public function renderCli() {
		return "$this->exceptionName [ " . $this->getErrorType() . " ]\n"
			. "Environment: " . $this->getEnvironment() . "\n"
			. "Line: " . $this->getErrorLine() . ". " . $this->getErrorFile() . "\n"
			. "Message: " . $this->getErrorMessage() . "\n"
			. "Time: " . date('Y/m/d H:i:s T') . "\n";
	}
	public function renderHtml() {
		return '<body style="padding:0;margin:0;background:black;color:whitesmoke;">'
			. '<div style="padding:10px;background:red;font-size:18px;">' . $this->exceptionName . ' [ ' . $this->getErrorType() . ' ] </div>'
			. '<div style="padding:10px;background:black;font-size:14px;color:yellow;line-height:1.5em;">'
			. '<font color="whitesmoke">Environment: </font>' . $this->getEnvironment() . '<br/>'
			. '<font color="whitesmoke">Line: </font>' . $this->getErrorLine() . ' [ ' . $this->getErrorFile(TRUE) . ' ]<br/>'
			. '<font color="whitesmoke">Message: </font>' . htmlspecialchars($this->getErrorMessage()) . '</br>'
			. '<font color="whitesmoke">Time: </font>' . date('Y/m/d H:i:s T') . '</div>'
			. '</body>';
	}
	public function renderJson() {
		$render = \Soter::getConfig()->getExceptionJsonRender();
		if (is_callable($render)) {
			return $render($this);
		}
		return '';
	}
	public function setHttpHeader() {
		if (!\Sr::isCli()) {
			header($this->httpStatusLine);
		}
		return $this;
	}
	public function __toString() {
		return $this->render(FALSE, TRUE);
	}
}
abstract class Soter_Session {
	protected $config;
	public function __construct($configFileName) {
		if (is_array($configFileName)) {
			$this->config = $configFileName;
		} else {
			$this->config = \Sr::config($configFileName);
		}
	}
	public abstract function init();
}

class Soter_Exception_404 extends Soter_Exception {
	protected $exceptionName = 'Soter_Exception_404',
			$httpStatusLine = 'HTTP/1.0 404 Not Found';
}
class Soter_Exception_500 extends Soter_Exception {
	protected $exceptionName = 'Soter_Exception_500',
			$httpStatusLine = 'HTTP/1.0 500 Internal Server Error';
}
class Soter_Exception_Database extends Soter_Exception {
	protected $exceptionName = 'Soter_Exception_Database',
			$httpStatusLine = 'HTTP/1.0 500 Internal Server Error';
}

class Soter_Request_Default implements Soter_Request {
	private $pathInfo, $queryString;
	public function __construct() {
		$this->pathInfo = \Sr::arrayGet($_SERVER, 'PATH_INFO', \Sr::arrayGet($_SERVER, 'REDIRECT_PATH_INFO'));
		$this->queryString = \Sr::arrayGet($_SERVER, 'QUERY_STRING', '');
	}
	public function getPathInfo() {
		return $this->pathInfo;
	}
	public function getQueryString() {
		return $this->queryString;
	}
	public function setPathInfo($pathInfo) {
		$this->pathInfo = $pathInfo;
		return $this;
	}
	public function setQueryString($queryString) {
		$this->queryString = $queryString;
		return $this;
	}
}
class Soter_View {
	private static $vars = array();
	public function add($key, $value = array()) {
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				if (!\Sr::arrayKeyExists($k, self::$vars)) {
					self::$vars[$k] = $v;
				}
			}
		} else {
			if (!\Sr::arrayKeyExists($key, self::$vars)) {
				self::$vars[$key] = $value;
			}
		}
		return $this;
	}
	public function set($key, $value = array()) {
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				self::$vars[$k] = $v;
			}
		} else {
			self::$vars[$key] = $value;
		}
		return $this;
	}
	private function _load($path, $data = array(), $return = false) {
		if (!file_exists($path)) {
			throw new \Soter_Exception_500('view file : [ ' . $path . ' ] not found');
		}
		$data = array_merge(self::$vars, $data);
		if (!empty($data)) {
			extract($data);
		}
		if ($return) {
			@ob_start();
			include $path;
			$html = ob_get_contents();
			@ob_end_clean();
			return $html;
		} else {
			include $path;
			return;
		}
	}
	/**
	 * 加载一个视图<br/>
	 * @param string $viewName 视图名称
	 * @param array  $data     视图中可以使用的数据
	 * @param bool   $return   是否返回视图内容
	 * @return string
	 */
	public function load($viewName, $data = array(), $return = false) {
		$config = \Sr::config();
		$path = $config->getApplicationDir() . $config->getViewsDirName() . '/' . $viewName . '.php';
		$hmvcModules = $config->getHmvcModules();
		$hmvcDirName = \Sr::arrayGet($hmvcModules, $config->getRoute()->getHmvcModuleName(), '');
		//当load方法在主项目的视图中被调用，然后hmvc主项目load了这个视图，那么这个视图里面的load应该使用的是主项目视图。
		//hmvc访问
		if ($hmvcDirName) {
			$hmvcPath = \Sr::realPath($config->getPrimaryApplicationDir() . $config->getHmvcDirName() . '/' . $hmvcDirName);
			$trace = debug_backtrace();
			$calledIsInHmvc = false;
			$appPath = \Sr::realPath($config->getApplicationDir());
			foreach ($trace as $t) {
				$filepath = \Sr::arrayGet($t, 'file', '');
				if (!empty($filepath)) {
					$filepath = \Sr::realPath($filepath);
					$checkList = array('load', 'runWeb', 'message', 'redirect');
					$function = \Sr::arrayGet($t, 'function', '');
					if ($filepath && in_array($function, $checkList) && strpos($filepath, $appPath) === 0 && strpos($filepath, $hmvcPath) === 0) {
						$calledIsInHmvc = true;
						break;
					} elseif (!in_array($function, $checkList)) {
						break;
					}
				}
			}
			//发现load是在主项目中被调用的，使用主项目视图
			if (!$calledIsInHmvc) {
				$path = $config->getPrimaryApplicationDir() . $config->getViewsDirName() . '/' . $viewName . '.php';
			}
		}
		return $this->_load($path, $data, $return);
	}
	/**
	 * 加载主项目的视图<br/>
	 * 这个一般是在hmvc模块中使用到，用于复用主项目的视图文件，比如通用的header等。<br/>
	 * @param string $viewName 主项目视图名称
	 * @param array  $data     视图中可以使用的数据
	 * @param bool   $return   是否返回视图内容
	 * @return string
	 */
	public function loadParent($viewName, $data = array(), $return = false) {
		$config = \Sr::config();
		$path = $config->getPrimaryApplicationDir() . $config->getViewsDirName() . '/' . $viewName . '.php';
		return $this->_load($path, $data, $return);
	}
}
class Soter_CliArgs {
	private $args;
	public function __construct() {
		$this->args = \Sr::getOpt();
	}
	public function get($key = null, $default = null) {
		if (empty($key)) {
			return $this->args;
		}
		return \Sr::arrayGet($this->args, $key, $default);
	}
}
class Soter_Route {
	private $found = false;
	private $controller, $method, $args, $hmvcModuleName;
	public function getHmvcModuleName() {
		return $this->hmvcModuleName;
	}
	public function setHmvcModuleName($hmvcModuleName) {
		$this->hmvcModuleName = $hmvcModuleName;
		return $this;
	}
	public function found() {
		return $this->found;
	}
	public function setFound($found) {
		$this->found = $found;
		return $this;
	}
	public function getController() {
		return $this->controller;
	}
	public function getMethod() {
		return $this->method;
	}
	public function getControllerShort() {
		return preg_replace('/^' . \Sr::config()->getControllerDirName() . '_/', '', $this->getController());
	}
	public function getMethodShort() {
		return preg_replace('/^' . \Sr::config()->getMethodPrefix() . '/', '', $this->getMethod());
	}
	public function getArgs() {
		return $this->args;
	}
	public function __construct() {
		$this->args = array();
	}
	public function setController($controller) {
		$this->controller = $controller;
		return $this;
	}
	public function setMethod($method) {
		$this->method = $method;
		return $this;
	}
	public function setArgs(array $args) {
		$this->args = $args;
		return $this;
	}
}
class Soter_Router_Get_Default extends Soter_Router {
	public function find() {
		$config = \Sr::config();
		$query = $config->getRequest()->getQueryString();
		//pathinfo非空说明是pathinfo路由，get路由器不再处理直接返回
		if ($config->getRequest()->getPathInfo() || !$query) {
			return $this->route->setFound(FALSE);
		}
		parse_str($query, $get);
		$controllerName = \Sr::arrayGet($get, $config->getRouterUrlControllerKey(), '');
		$methodName = \Sr::arrayGet($get, $config->getRouterUrlMethodKey(), '');
		$hmvcModule = \Sr::arrayGet($get, $config->getRouterUrlModuleKey(), '');
		$_hmvcModule = $config->getCurrentDomainHmvcModuleNname();
		if (!$_hmvcModule) {
			if ($config->hmvcIsDomainOnly($hmvcModule)) {
				//当前域名没有绑定任何hmvc模块，而且当前hmvc模块是domainOnly的，禁止访问当前hmvc模块
				$hmvcModule = '';
			}
		} else {
			//当前域名绑定了hmvc模块，就重置$hmvcModule为绑定的hvmc模块
			$hmvcModule = $_hmvcModule;
		}
		//处理hmvc模块
		$hmvcModuleDirName = \Soter::checkHmvc($hmvcModule, false);
		if ($controllerName) {
			$controllerName = $config->getControllerDirName() . '_' . $controllerName;
		}
		if ($methodName) {
			$methodName = $config->getMethodPrefix() . $methodName;
		}
		return $this->route->setHmvcModuleName($hmvcModuleDirName ? $hmvcModule : '')
				->setController($controllerName)
				->setMethod($methodName)
				->setFound($hmvcModuleDirName || $controllerName);
	}
}
class Soter_Router_PathInfo_Default extends Soter_Router {
	public function find() {
		$config = \Soter::getConfig();
		$uri = $config->getRequest()->getPathInfo();
		$uri = trim($uri, '/');
		if (empty($uri)) {
			//没有找到hmvc模块名称，或者控制器名称
			return $this->route->setFound(FALSE);
		} else {
			if ($uriRewriter = $config->getUriRewriter()) {
				$uri = $uriRewriter->rewrite($uri);
			}
		}
		//到此$uri形如：Welcome/index.do , Welcome/User , Welcome
		$_info = explode('/', $uri);
		$hmvcModule = current($_info);
		//当前域名绑定了hvmc模块$_hmvcModule就是模块名称，反之为空
		$_hmvcModule = $config->getCurrentDomainHmvcModuleNname();
		if (!$_hmvcModule) {
			if ($config->hmvcIsDomainOnly($hmvcModule)) {
				//当前域名没有绑定任何hmvc模块，而且当前hmvc模块是domainOnly的，禁止访问当前hmvc模块
				$hmvcModule = '';
			}
		} else {
			//当前域名绑定了hmvc模块，那么当前域名就指向固定的配置的hmvc模块，重置$hmvcModule为绑定的hvmc模块
			$hmvcModule = $_hmvcModule;
		}
		//处理hmvc模块
		$hmvcModuleDirName = \Soter::checkHmvc($hmvcModule, FALSE);
		if (!$_hmvcModule && $hmvcModuleDirName && !$config->hmvcIsDomainOnly($hmvcModule)) {
			//当前域名没有绑定hvmc,且访问的是hmvc模块，且是非domainOnly的，那么就去除hmvc模块名称，得到真正的路径
			$uri = ltrim(substr($uri, strlen($hmvcModule)), '/');
		}
		//首先控制器名和方法名初始化为默认
		$controller = $config->getDefaultController();
		$method = $config->getDefaultMethod();
		$subfix = $config->getMethodUriSubfix();
		/**
		 * 到此，如果上面$uri被去除掉hmvc模块名称后，$uri有可能是空
		 * 或者$uri有控制器名称或者方法-参数名称
		 * 形如：1.Welcome/article.do , 2.Welcome/article-001.do ,
		 *      3.article-001.do ,4.article.do , 5.Welcome/User , 6.Welcome
		 */
		if ($uri) {
			//解析路径
			$methodPathArr = explode($subfix, $uri);
			//找到了控制器名或者方法-参数名(1,2,3,4)
			if (\Sr::strEndsWith($uri, $subfix)) {
				//找到了控制器名和方法-参数名(1,2)，覆盖上面的默认控制器名和方法-参数名
				if (stripos($methodPathArr[0], '/') !== false) {
					$controller = str_replace('/', '_', dirname($uri));
					$method = basename($methodPathArr[0]);
				} else {
					//只找到了方法-参数名(3,4)，覆盖上面的默认方法名
					$method = basename($methodPathArr[0]);
				}
			} else {
				//只找到了控制器名(5,6)，覆盖上面的默认控制器名
				$controller = str_replace('/', '_', $uri);
			}
		}
		$controller = $config->getControllerDirName() . '_' . $controller;
		//统一解析方法-参数名
		$methodAndParameters = explode($config->getMethodParametersDelimiter(), $method);
		$method = $config->getMethodPrefix() . current($methodAndParameters);
		array_shift($methodAndParameters);
		$parameters = $methodAndParameters;
		return $this->route
				->setHmvcModuleName($hmvcModuleDirName ? $hmvcModule : '')
				->setController($controller)
				->setMethod($method)
				->setArgs($parameters)
				->setFound(TRUE);
	}
}
/**
 * @property Soter_Exception_Handle $exceptionHandle
 */
class Soter_Config {
	private $applicationDir = '', //项目目录
		$primaryApplicationDir = '', //主项目目录
		$indexDir = '', //入口文件目录
		$indexName = '', //入口文件名称
		$classesDirName = 'classes',
		$hmvcDirName = 'hmvc',
		$libraryDirName = 'library',
		$functionsDirName = 'functions',
		$storageDirPath = '',
		$viewsDirName = 'views',
		$configDirName = 'config',
		$controllerDirName = 'Controller',
		$businessDirName = 'Business',
		$daoDirName = 'Dao',
		$beanDirName = 'Bean',
		$modelDirName = 'Model',
		$taskDirName = 'Task',
		$defaultController = 'Welcome',
		$defaultMethod = 'index',
		$methodPrefix = 'do_',
		$methodUriSubfix = '.do',
		$routerUrlModuleKey = 'm',
		$routerUrlControllerKey = 'c',
		$routerUrlMethodKey = 'a',
		$methodParametersDelimiter = '-',
		$logsSubDirNameFormat = 'Y-m-d/H',
		$cookiePrefix = '',
		$backendServerIpWhitelist = array(),
		$isRewrite = FALSE,
		$request, $showError = true,
		$routersContainer = array(),
		$packageMasterContainer = array(),
		$packageContainer = array(),
		$loggerWriterContainer = array(),
		$uriRewriter,
		$exceptionHandle,
		$route,
		$environment = 'development',
		$hmvcModules = array(),
		$isMaintainMode = false,
		$maintainIpWhitelist = array(),
		$maintainModeHandle,
		$databseConfig,
		$cacheHandles = array(),
		$cacheConfig,
		$sessionConfig,
		$sessionHandle,
		$methodCacheConfig,
		$dataCheckRules,
		$outputJsonRender,
		$exceptionJsonRender,
		$srMethods = array(),
		$encryptKey,
		$hmvcDomains = array(),
		$errorMemoryReserveSize = 512000
	;
	/**
	 * 按照包的顺序查找配置文件
	 * @param type $filename
	 * @return string
	 */
	public function find($filename) {
		foreach ($this->getPackages() as $packagePath) {
			$path = $packagePath . $this->getConfigDirName() . '/';
			$filePath = $path . $this->getEnvironment() . '/' . $filename . '.php';
			$fileDefaultPath = $path . 'default/' . $filename . '.php';
			if (file_exists($filePath)) {
				return $filePath;
			} elseif (file_exists($fileDefaultPath)) {
				return $fileDefaultPath;
			}
		}
		return "";
	}
	public function getExceptionMemoryReserveSize() {
		return $this->errorMemoryReserveSize;
	}
	public function setExceptionMemoryReserveSize($exceptionMemoryReserveSize) {
		$this->errorMemoryReserveSize = $exceptionMemoryReserveSize;
		return $this;
	}
	public function setExceptionControl($isExceptionControl) {
		if ($isExceptionControl && !\Sr::isPluginMode()) {
			//注册错误处理
			\Soter_Logger_Writer_Dispatcher::initialize();
		}
		return $this;
	}
	public function getStorageDirPath() {
		return empty($this->storageDirPath) ? $this->getPrimaryApplicationDir() . 'storage/' : $this->storageDirPath;
	}
	public function setStorageDirPath($storageDirPath) {
		$this->storageDirPath = \Sr::realPath($storageDirPath, true);
		return $this;
	}
	public function getCurrentDomainHmvcModuleNname() {
		if (!$this->hmvcDomains['enable']) {
			return false;
		}
		$_domain = \Sr::server('http_host');
		$domain = explode('.', $_domain);
		$length = count($domain);
		$topDomain = '';
		if ($length >= 2) {
			$topDomain = $domain[$length - 2] . '.' . $domain[$length - 1];
		}
		foreach ($this->hmvcDomains['domains'] as $prefix => $hvmc) {
			if (($hvmc['isFullDomain'] ? $prefix : ($prefix . '.' . $topDomain)) == $_domain) {
				return $hvmc['enable'] ? $hvmc['hmvcModuleName'] : false;
			}
		}
		return '';
	}
	public function hmvcIsDomainOnly($hmvcModuleName) {
		if (!$hmvcModuleName || !$this->hmvcDomains['enable']) {
			return false;
		}
		foreach ($this->hmvcDomains['domains'] as $hvmc) {
			if ($hmvcModuleName == $hvmc['hmvcModuleName'] && $hvmc['enable']) {
				return $hvmc['domainOnly'];
			}
		}
		return false;
	}
	public function setHmvcDomains(Array $hmvcDomains) {
		$this->hmvcDomains = $hmvcDomains;
		return $this;
	}
	public function getEncryptKey() {
		$key = $this->getEnvironment();
		if (isset($this->encryptKey[$key])) {
			return $this->encryptKey[$key];
		} elseif (isset($this->encryptKey['default'])) {
			return $this->encryptKey['default'];
		}
		return '';
	}
	public function setEncryptKey($encryptKey) {
		if (is_array($encryptKey)) {
			$this->encryptKey = $encryptKey;
		} else {
			$this->encryptKey = array(
			    'default' => $encryptKey,
			);
		}
		return $this;
	}
	public function getSrMethods() {
		return $this->srMethods;
	}
	public function setSrMethods(array $srMethods) {
		$this->srMethods = $srMethods;
		return $this;
	}
	public function getExceptionJsonRender() {
		return $this->exceptionJsonRender;
	}
	public function setExceptionJsonRender($exceptionJsonRender) {
		$this->exceptionJsonRender = $exceptionJsonRender;
		return $this;
	}
	public function getOutputJsonRender() {
		return $this->outputJsonRender;
	}
	public function setOutputJsonRender($outputJsonHandle) {
		$this->outputJsonRender = $outputJsonHandle;
		return $this;
	}
	public function getDataCheckRules() {
		return $this->dataCheckRules;
	}
	public function setDataCheckRules($dataCheckRules) {
		$this->dataCheckRules = is_array($dataCheckRules) ? $dataCheckRules : \Sr::config($dataCheckRules, false);
		return $this;
	}
	public function getMethodCacheConfig() {
		return $this->methodCacheConfig;
	}
	public function setMethodCacheConfig($methodCacheConfig) {
		$this->methodCacheConfig = is_array($methodCacheConfig) ? $methodCacheConfig : \Sr::config($methodCacheConfig, false);
		return $this;
	}
	public function getViewsDirName() {
		return $this->viewsDirName;
	}
	public function setViewsDirName($viewsDirName) {
		$this->viewsDirName = $viewsDirName;
		return $this;
	}
	/**
	 *
	 * @return Soter_Cache
	 */
	public function getCacheHandle($key = '') {
		if (empty($this->cacheConfig)) {
			$this->cacheConfig = array(
			    'default_type' => 'file',
			    'drivers' => array(
				'file' => array(
				    'class' => 'Soter_Cache_File',
				    //缓存文件保存路径
				    'config' => \Sr::config()->getStorageDirPath() . 'cache/'
				),
			    )
			);
		}
		if (is_array($key)) {
			$className = $key['class'];
			$config = $key['config'];
			return is_null($config) ? new $className() : new $className($config);
		} else {
			$key = $key ? $key : $this->cacheConfig['default_type'];
			if (!\Sr::arrayKeyExists("drivers.$key", $this->cacheConfig)) {
				throw new \Soter_Exception_500('unknown cache type [ ' . $key . ' ]');
			}
			$config = $this->cacheConfig['drivers'][$key]['config'];
			$className = $this->cacheConfig['drivers'][$key]['class'];
			if (!\Sr::arrayKeyExists($key, $this->cacheHandles)) {
				$this->cacheHandles[$key] = is_null($config) ? new $className() : new $className($config);
			}
			return $this->cacheHandles[$key];
		}
	}
	public function getCacheConfig() {
		return $this->cacheConfig;
	}
	public function setCacheConfig($cacheConfig) {
		$this->cacheHandles = array();
		if (is_string($cacheConfig)) {
			$this->cacheConfig = \Sr::config($cacheConfig, false);
		} elseif (is_array($cacheConfig)) {
			$this->cacheConfig = $cacheConfig;
		} else {
			throw new \Soter_Exception_500('unknown type of cache configure , it should be a string or an array .');
		}
		return $this;
	}
	/**
	 *
	 * @return Soter_Session
	 */
	public function getSessionHandle() {
		return $this->sessionHandle;
	}
	public function setSessionHandle($sessionHandle) {
		if ($sessionHandle instanceof Soter_Session) {
			$this->sessionHandle = $sessionHandle;
		} else {
			$this->sessionHandle = \Sr::config($sessionHandle, false);
		}
		return $this;
	}
	public function getSessionConfig() {
		if (empty($this->sessionConfig)) {
			$this->sessionConfig = array(
			    'autostart' => false,
			    'cookie_path' => '/',
			    'cookie_domain' => \Sr::server('HTTP_HOST'),
			    'session_name' => 'SOTER',
			    'lifetime' => 3600,
			);
		}
		return $this->sessionConfig;
	}
	public function setSessionConfig($sessionConfig) {
		if (is_array($sessionConfig)) {
			$this->sessionConfig = $sessionConfig;
		} else {
			$this->sessionConfig = \Sr::config($sessionConfig, false);
		}
		return $this;
	}
	public function getDatabseConfig($group = null) {
		if (empty($group)) {
			return $this->databseConfig;
		} else {
			return \Sr::arrayKeyExists($group, $this->databseConfig) ? $this->databseConfig[$group] : array();
		}
	}
	public function setDatabseConfig($databseConfig) {
		\Sr::clearDbInstances();
		$this->databseConfig = is_array($databseConfig) ? $databseConfig : \Sr::config($databseConfig, false);
		return $this;
	}
	public function getIsMaintainMode() {
		return $this->isMaintainMode;
	}
	public function getMaintainModeHandle() {
		return $this->maintainModeHandle;
	}
	public function setIsMaintainMode($isMaintainMode) {
		$this->isMaintainMode = $isMaintainMode;
		return $this;
	}
	public function setMaintainModeHandle(Soter_Maintain_Handle $maintainModeHandle) {
		$this->maintainModeHandle = $maintainModeHandle;
		return $this;
	}
	public function getMaintainIpWhitelist() {
		return $this->maintainIpWhitelist;
	}
	public function setMaintainIpWhitelist($maintainIpWhitelist) {
		$this->maintainIpWhitelist = $maintainIpWhitelist;
		return $this;
	}
	public function getMethodParametersDelimiter() {
		return $this->methodParametersDelimiter;
	}
	public function setMethodParametersDelimiter($methodParametersDelimiter) {
		$this->methodParametersDelimiter = $methodParametersDelimiter;
		return $this;
	}
	public function getRouterUrlModuleKey() {
		return $this->routerUrlModuleKey;
	}
	public function getRouterUrlControllerKey() {
		return $this->routerUrlControllerKey;
	}
	public function getRouterUrlMethodKey() {
		return $this->routerUrlMethodKey;
	}
	public function setRouterUrlModuleKey($routerUrlModuleKey) {
		$this->routerUrlModuleKey = $routerUrlModuleKey;
		return $this;
	}
	public function setRouterUrlControllerKey($routerUrlControllerKey) {
		$this->routerUrlControllerKey = $routerUrlControllerKey;
		return $this;
	}
	public function setRouterUrlMethodKey($routerUrlMethodKey) {
		$this->routerUrlMethodKey = $routerUrlMethodKey;
		return $this;
	}
	/**
	 *
	 * @return Soter_Uri_Rewriter
	 */
	public function getUriRewriter() {
		return $this->uriRewriter;
	}
	public function setUriRewriter(Soter_Uri_Rewriter $uriRewriter) {
		$this->uriRewriter = $uriRewriter;
		return $this;
	}
	public function getPrimaryApplicationDir() {
		return $this->primaryApplicationDir;
	}
	public function setPrimaryApplicationDir($primaryApplicationDir) {
		$this->primaryApplicationDir = \Sr::realPath($primaryApplicationDir) . '/';
		return $this;
	}
	public function getBackendServerIpWhitelist() {
		return $this->backendServerIpWhitelist;
	}
	/**
	 * 如果服务器是ngix之类代理转发请求到后端apache运行的PHP<br>
	 * 那么这里应该设置信任的nginx所在服务器的ip<br>
	 * nginx里面应该设置 X_FORWARDED_FOR server变量来表示真实的客户端IP<br>
	 * 不然通过Sr::clientIp()是获取不到真实的客户端IP的<br>
	 * @param type $backendServerIpWhitelist
	 * @return \Soter_Config
	 */
	public function setBackendServerIpWhitelist(Array $backendServerIpWhitelist) {
		$this->backendServerIpWhitelist = $backendServerIpWhitelist;
		return $this;
	}
	public function getCookiePrefix() {
		return $this->cookiePrefix;
	}
	public function setCookiePrefix($cookiePrefix) {
		$this->cookiePrefix = $cookiePrefix;
		return $this;
	}
	public function getLogsSubDirNameFormat() {
		return $this->logsSubDirNameFormat;
	}
	/**
	 * 设置日志子目录格式，参数就是date()函数的第一个参数,默认是 Y-m-d/H
	 * @param type $logsSubDirNameFormat
	 * @return \Soter_Config
	 */
	public function setLogsSubDirNameFormat($logsSubDirNameFormat) {
		$this->logsSubDirNameFormat = $logsSubDirNameFormat;
		return $this;
	}
	public function addAutoloadFunctions(Array $funciontsFileNameArray) {
		foreach ($funciontsFileNameArray as $functionsFileName) {
			\Sr::functions($functionsFileName);
		}
		return $this;
	}
	public function getFunctionsDirName() {
		return $this->functionsDirName;
	}
	public function setFunctionsDirName($functionsDirName) {
		$this->functionsDirName = $functionsDirName;
		return $this;
	}
	public function getModelDirName() {
		return $this->modelDirName;
	}
	public function setModelDirName($modelDirName) {
		$this->modelDirName = $modelDirName;
		return $this;
	}
	public function getBeanDirName() {
		return $this->beanDirName;
	}
	public function setBeanDirName($beanDirName) {
		$this->beanDirName = $beanDirName;
		return $this;
	}
	public function getBusinessDirName() {
		return $this->businessDirName;
	}
	public function getDaoDirName() {
		return $this->daoDirName;
	}
	public function getTaskDirName() {
		return $this->taskDirName;
	}
	public function setBusinessDirName($businessDirName) {
		$this->businessDirName = $businessDirName;
		return $this;
	}
	public function setDaoDirName($daoDirName) {
		$this->daoDirName = $daoDirName;
		return $this;
	}
	public function setTaskDirName($taskDirName) {
		$this->taskDirName = $taskDirName;
		return $this;
	}
	public function getEnvironment() {
		return $this->environment;
	}
	public function setEnvironment($environment) {
		$this->environment = $environment;
		return $this;
	}
	public function getConfigDirName() {
		return $this->configDirName;
	}
	public function setConfigDirName($configDirName) {
		$this->configDirName = $configDirName;
		return $this;
	}
	/**
	 *
	 * @return Soter_Route
	 */
	public function getRoute() {
		return empty($this->route) ? new \Soter_Route() : $this->route;
	}
	public function setRoute($route) {
		$this->route = $route;
		return $this;
	}
	public function getLibraryDirName() {
		return $this->libraryDirName;
	}
	public function setLibraryDirName($libraryDirName) {
		$this->libraryDirName = $libraryDirName;
		return $this;
	}
	public function getHmvcDirName() {
		return $this->hmvcDirName;
	}
	public function setHmvcDirName($hmvcDirName) {
		$this->hmvcDirName = $hmvcDirName;
		return $this;
	}
	public function getHmvcModules() {
		return $this->hmvcModules;
	}
	public function setHmvcModules($hmvcModules) {
		$this->hmvcModules = $hmvcModules;
		return $this;
	}
	public function getControllerDirName() {
		return $this->controllerDirName;
	}
	public function setControllerDirName($controllerDirName) {
		$this->controllerDirName = $controllerDirName;
		return $this;
	}
	public function getExceptionHandle() {
		return $this->exceptionHandle;
	}
	public function setExceptionHandle($exceptionHandle) {
		$this->exceptionHandle = $exceptionHandle;
		return $this;
	}
	function setExceptionLevel($exceptionLevel) {
		error_reporting($exceptionLevel);
		return $this;
	}
	public function getApplicationDir() {
		return $this->applicationDir;
	}
	public function getIndexDir() {
		return $this->indexDir;
	}
	public function getIndexName() {
		return $this->indexName;
	}
	public function setApplicationDir($applicationDir) {
		$this->applicationDir = \Sr::realPath($applicationDir) . '/';
		if (empty($this->primaryApplicationDir)) {
			$this->primaryApplicationDir = $this->applicationDir;
		}
		return $this;
	}
	public function setIndexDir($indexDir) {
		$this->indexDir = \Sr::realPath($indexDir) . '/';
		return $this;
	}
	public function setIndexName($indexName) {
		$this->indexName = $indexName;
		return $this;
	}
	public function setLoggerWriterContainer(Soter_Logger_Writer $loggerWriterContainer) {
		$this->loggerWriterContainer = $loggerWriterContainer;
		return $this;
	}
	public function getMethodPrefix() {
		return $this->methodPrefix;
	}
	public function getMethodUriSubfix() {
		return $this->methodUriSubfix;
	}
	public function setMethodPrefix($methodPrefix) {
		$this->methodPrefix = $methodPrefix;
		return $this;
	}
	public function setMethodUriSubfix($methodUriSubfix) {
		if (!$methodUriSubfix) {
			throw new \Soter_Exception_500('"Method Uri Subfix" can not be empty.');
		}
		$this->methodUriSubfix = $methodUriSubfix;
		return $this;
	}
	public function getDefaultController() {
		return $this->defaultController;
	}
	public function getDefaultMethod() {
		return $this->defaultMethod;
	}
	public function setDefaultController($defaultController) {
		$this->defaultController = $defaultController;
		return $this;
	}
	public function setDefaultMethod($defaultMethod) {
		$this->defaultMethod = $defaultMethod;
		return $this;
	}
	public function getClassesDirName() {
		return $this->classesDirName;
	}
	public function setClassesDirName($classesDirName) {
		$this->classesDirName = $classesDirName;
		return $this;
	}
	public function getPackages() {
		return array_merge($this->packageMasterContainer, $this->packageContainer);
	}
	public function addMasterPackages(Array $packagesPath) {
		foreach ($packagesPath as $packagePath) {
			$this->addMasterPackage($packagePath);
		}
		return $this;
	}
	public function addMasterPackage($packagePath) {
		$packagePath = \Sr::realPath($packagePath) . '/';
		if (!in_array($packagePath, $this->packageMasterContainer)) {
			//注册“包”到主包容器中
			array_push($this->packageMasterContainer, $packagePath);
			if (file_exists($library = $packagePath . $this->getLibraryDirName() . '/')) {
				array_push($this->packageMasterContainer, $library);
			}
		}
		return $this;
	}
	public function addPackages(Array $packagesPath) {
		foreach ($packagesPath as $packagePath) {
			$this->addPackage($packagePath);
		}
		return $this;
	}
	public function addPackage($packagePath) {
		$packagePath = \Sr::realPath($packagePath) . '/';
		if (!in_array($packagePath, $this->packageContainer)) {
			//注册“包”到包容器中
			array_push($this->packageContainer, $packagePath);
			if (file_exists($library = $packagePath . $this->getLibraryDirName() . '/')) {
				array_push($this->packageContainer, $library);
			}
		}
		return $this;
	}
	/**
	 * 加载项目目录下的bootstrap.php配置
	 */
	public function bootstrap() {
		//引入“bootstrap”配置
		if (file_exists($bootstrap = $this->getApplicationDir() . 'bootstrap.php')) {
			\Sr::includeOnce($bootstrap);
		}
	}
	public function getShowError() {
		return $this->showError;
	}
	public function getRoutersContainer() {
		return $this->routersContainer;
	}
	public function setShowError($showError) {
		$this->showError = $showError;
		return $this;
	}
	/**
	 *
	 * @return Soter_Request
	 */
	public function getRequest() {
		return $this->request;
	}
	public function setRequest(Soter_Request $request) {
		$this->request = $request;
		return $this;
	}
	public function addRouter(Soter_Router $router) {
		array_unshift($this->routersContainer, $router);
		return $this;
	}
	public function getRouters() {
		return $this->routersContainer;
	}
	public function addLoggerWriter(Soter_Logger_Writer $loggerWriter) {
		$this->loggerWriterContainer[] = $loggerWriter;
		return $this;
	}
	public function getLoggerWriters() {
		return $this->loggerWriterContainer;
	}
	public function getIsRewrite() {
		return $this->isRewrite;
	}
	public function setTimeZone($timeZone) {
		date_default_timezone_set($timeZone);
		return $this;
	}
	public function setIsRewrite($isRewrite) {
		$this->isRewrite = $isRewrite;
		return $this;
	}
}
class Soter_Logger_Writer_Dispatcher {
	private static $instance;
	private static $memReverse;
	public static function initialize() {
		if (empty(self::$instance)) {
			//保留内存
			self::$memReverse = str_repeat("x", \Soter::getConfig()->getExceptionMemoryReserveSize());
			self::$instance = new \Soter_Logger_Writer_Dispatcher();
			//插件模式打开错误显示，web和命令行模式关闭错误显示
			\Sr::isPluginMode() ? ini_set('display_errors', TRUE) : ini_set('display_errors', FALSE);
			set_exception_handler(array(self::$instance, 'handleException'));
			set_error_handler(array(self::$instance, 'handleError'));
			register_shutdown_function(array(self::$instance, 'handleFatal'));
		}
	}
	final public function handleException($exception) {
		if (is_subclass_of($exception, 'Soter_Exception')) {
			$this->dispatch($exception);
		} else {
			$this->dispatch(new \Soter_Exception_500($exception->getMessage(), $exception->getCode(), get_class($exception), $exception->getFile(), $exception->getLine()));
		}
	}
	final public function handleError($code, $message, $file, $line) {
		if (0 == error_reporting()) {
			return;
		}
		$this->dispatch(new \Soter_Exception_500($message, $code, 'General Error', $file, $line));
	}
	final public function handleFatal() {
		if (0 == error_reporting()) {
			return;
		}
		$lastError = error_get_last();
		$fatalError = array(1, 256, 64, 16, 4, 4096);
		if (!\Sr::arrayKeyExists("type", $lastError) || !in_array($lastError["type"], $fatalError)) {
			return;
		}
		//当发生致命错误的时候，释放保留的内存，提供给下面的处理代码使用
		self::$memReverse = null;
		$this->dispatch(new \Soter_Exception_500($lastError['message'], $lastError['type'], 'Fatal Error', $lastError['file'], $lastError['line']));
	}
	final public function dispatch(Soter_Exception $exception) {
		$config = \Sr::config();
		ini_set('display_errors', TRUE);
		$loggerWriters = $config->getLoggerWriters();
		foreach ($loggerWriters as $loggerWriter) {
			$loggerWriter->write($exception);
		}
		if ($config->getShowError()) {
			$handle = $config->getExceptionHandle();
			if ($handle instanceof Soter_Exception_Handle) {
				$handle->handle($exception);
			} else {
				$exception->render();
			}
		} elseif (\Sr::isCli()) {
			$exception->render();
		}
		exit();
	}
}
class Soter_Logger_FileWriter implements Soter_Logger_Writer {
	private $logsDirPath, $log404;
	public function __construct($logsDirPath, $log404 = true) {
		$this->log404 = $log404;
		$this->logsDirPath = \Sr::realPath($logsDirPath) . '/' . date(\Sr::config()->getLogsSubDirNameFormat()) . '/';
	}
	public function write(Soter_Exception $exception) {
		if (!$this->log404 && ($exception instanceof Soter_Exception_404)) {
			return;
		}
		$content = 'Domain : ' . \Sr::server('http_host') . "\n"
			. 'ClientIP : ' . \Sr::server('SERVER_ADDR') . "\n"
			. 'ServerIP : ' . \Sr::serverIp() . "\n"
			. 'ServerHostname : ' . \Sr::hostname() . "\n"
			. (!\Sr::isCli() ? 'Request Uri : ' . \Sr::server('request_uri') : '') . "\n"
			. (!\Sr::isCli() ? 'Get Data : ' . json_encode(\Sr::get()) : '') . "\n"
			. (!\Sr::isCli() ? 'Post Data : ' . json_encode(\Sr::post()) : '') . "\n"
			. (!\Sr::isCli() ? 'Cookie Data : ' . json_encode(\Sr::cookie()) : '') . "\n"
			. (!\Sr::isCli() ? 'Server Data : ' . json_encode(\Sr::server()) : '') . "\n"
			. $exception->renderCli() . "\n";
		if (!is_dir($this->logsDirPath)) {
			mkdir($this->logsDirPath, 0700, true);
		}
		if (!file_exists($logsFilePath = $this->logsDirPath . 'logs.php')) {
			$content = '<?php defined("IN_SOTER") or exit();?>' . "\n" . $content;
		}
		file_put_contents($logsFilePath, $content, LOCK_EX | FILE_APPEND);
	}
}
class Soter_Maintain_Handle_Default implements Soter_Maintain_Handle {
	public function handle() {
		if (!\Sr::isCli()) {
			header('Content-type: text/html;charset=utf-8');
		}
		echo '<center><h2>server is under maintenance</h2><h3>服务器维护中</h3>' . date('Y/m/d H:i:s e') . '</center>';
	}
}
class Soter_Uri_Rewriter_Default implements Soter_Uri_Rewriter {
	public function rewrite($uri) {
		return $uri;
	}
}
class Soter_Exception_Handle_Default implements Soter_Exception_Handle {
	public function handle(Soter_Exception $exception) {
		$exception->render();
	}
}
class Soter_Database_SlowQuery_Handle_Default implements Soter_Database_SlowQuery_Handle {
	public function handle($sql, $explainString, $time) {
		$dir = \Sr::config()->getStorageDirPath() . 'slow-query-debug/';
		$file = $dir . 'slow-query-debug.php';
		if (!is_dir($dir)) {
			mkdir($dir, 0700, true);
		}
		$content = "\nSQL : " . $sql
			. "\nExplain : " . $explainString
			. "\nUsingTime : " . $time . " ms"
			. "\nTime : " . date('Y-m-d H:i:s') . "\n";
		if (!file_exists($file)) {
			$content = '<?php defined("IN_SOTER") or exit();?>' . "\n" . $content;
		}
		file_put_contents($file, $content, LOCK_EX | FILE_APPEND);
	}
}
class Soter_Database_Index_Handle_Default implements Soter_Database_Index_Handle {
	public function handle($sql, $explainString, $time) {
		$dir = \Sr::config()->getStorageDirPath() . 'index-debug/';
		$file = $dir . 'index-debug.php';
		if (!is_dir($dir)) {
			mkdir($dir, 0700, true);
		}
		$content = "\nSQL : " . $sql
			. "\nExplain : " . $explainString
			. "\nUsingTime : " . $time . " ms"
			. "\nTime : " . date('Y-m-d H:i:s') . "\n";
		if (!file_exists($file)) {
			$content = '<?php defined("IN_SOTER") or exit();?>' . "\n" . $content;
		}
		file_put_contents($file, $content, LOCK_EX | FILE_APPEND);
	}
}
class Soter_Cache_File implements Soter_Cache {
	private $_cacheDirPath;
	public function __construct($cacheDirPath = '') {
		$cacheDirPath = empty($cacheDirPath) ? \Sr::config()->getStorageDirPath() . 'cache/' : $cacheDirPath;
		$this->_cacheDirPath = \Sr::realPath($cacheDirPath) . '/';
		if (!is_dir($this->_cacheDirPath)) {
			mkdir($this->_cacheDirPath, 0700, true);
		}
		if (!is_writable($this->_cacheDirPath)) {
			throw new \Soter_Exception_500('cache dir [ ' . \Sr::safePath($this->_cacheDirPath) . ' ] not writable');
		}
	}
	private function _hashKey($key) {
		return md5($key);
	}
	private function _hashKeyPath($key) {
		$key = md5($key);
		$len = strlen($key);
		return $this->_cacheDirPath . $key{$len - 1} . '/' . $key{$len - 2} . '/' . $key{$len - 3} . '/';
	}
	private function pack($userData, $cacheTime) {
		$cacheTime = (int) $cacheTime;
		return @serialize(array(
			    'userData' => $userData,
			    'expireTime' => ($cacheTime == 0 ? 0 : time() + $cacheTime)
		));
	}
	private function unpack($cacheData) {
		$cacheData = @unserialize($cacheData);
		if (is_array($cacheData) && \Sr::arrayKeyExists('userData', $cacheData) && \Sr::arrayKeyExists('expireTime', $cacheData)) {
			if ($cacheData['expireTime'] == 0) {
				return $cacheData['userData'];
			}
			return $cacheData['expireTime'] > time() ? $cacheData['userData'] : NULL;
		} else {
			return NULL;
		}
	}
	public function clean() {
		return \Sr::rmdir($this->_cacheDirPath, false);
	}
	public function delete($key) {
		if (empty($key)) {
			return false;
		}
		$key = $this->_hashKey($key);
		$filePath = $this->_hashKeyPath($key) . $key;
		if (file_exists($filePath)) {
			return @unlink($filePath);
		}
		return true;
	}
	public function get($key) {
		if (empty($key)) {
			return null;
		}
		$key = $this->_hashKey($key);
		$filePath = $this->_hashKeyPath($key) . $key;
		if (file_exists($filePath)) {
			$cacheData = file_get_contents($filePath);
			$userData = $this->unpack($cacheData);
			return is_null($userData) ? null : $userData;
		}
		return NULL;
	}
	public function set($key, $value, $cacheTime = 0) {
		if (empty($key)) {
			return false;
		}
		$key = $this->_hashKey($key);
		$cacheDir = $this->_hashKeyPath($key);
		$filePath = $cacheDir . $key;
		if (!is_dir($cacheDir)) {
			mkdir($cacheDir, 0700, true);
		}
		$cacheData = $this->pack($value, $cacheTime);
		if (empty($cacheData)) {
			return false;
		}
		return file_put_contents($filePath, $cacheData, LOCK_EX);
	}
	public function &instance($key = null, $isRead = true) {
		return $this;
	}
	public function reset() {
		return $this;
	}
}
class Soter_Cache_Memcached implements Soter_Cache {
	private $config, $handle;
	public function __construct($config) {
		$this->config = $config;
	}
	private function _init() {
		if (empty($this->handle)) {
			$this->handle = new \Memcached();
			foreach ($this->config as $server) {
				if ($server[2] > 0) {
					$this->handle->addServer($server[0], $server[1], $server[2]);
				} else {
					$this->handle->addServer($server[0], $server[1]);
				}
			}
		}
	}
	public function clean() {
		$this->_init();
		return $this->handle->flush();
	}
	public function delete($key) {
		$this->_init();
		return $this->handle->delete($key);
	}
	public function get($key) {
		$this->_init();
		return ($data = $this->handle->get($key)) ? $data : null;
	}
	public function set($key, $value, $cacheTime = 0) {
		$this->_init();
		return $this->handle->set($key, $value, $cacheTime > 0 ? (time() + $cacheTime) : 0);
	}
	public function &instance($key = null, $isRead = true) {
		$this->_init();
		return $this->handle;
	}
	public function reset() {
		$this->handle = null;
		return $this;
	}
}
class Soter_Cache_Memcache implements Soter_Cache {
	private $config, $handle;
	public function __construct($config) {
		$this->config = $config;
	}
	private function _init() {
		if (empty($this->handle)) {
			$this->handle = new \Memcache();
			foreach ($this->config as $server) {
				$this->handle->addserver($server[0], $server[1]);
			}
		}
	}
	public function clean() {
		$this->_init();
		return $this->handle->flush();
	}
	public function delete($key) {
		$this->_init();
		return $this->handle->delete($key);
	}
	public function get($key) {
		$this->_init();
		return ($data = $this->handle->get($key)) ? $data : null;
	}
	public function set($key, $value, $cacheTime = 0) {
		$this->_init();
		return $this->handle->set($key, $value, false, $cacheTime);
	}
	public function &instance($key = null, $isRead = true) {
		$this->_init();
		return $this->handle;
	}
	public function reset() {
		$this->handle = null;
		return $this;
	}
}
class Soter_Cache_Apc implements Soter_Cache {
	public function clean() {
		@apc_clear_cache();
		@apc_clear_cache("user");
		return true;
	}
	public function delete($key) {
		return apc_delete($key);
	}
	public function get($key) {
		$data = apc_fetch($key, $bo);
		if ($bo === false) {
			return null;
		}
		return $data;
	}
	public function set($key, $value, $cacheTime = 0) {
		return apc_store($key, $value, $cacheTime);
	}
	public function &instance($key = null, $isRead = true) {
		return $this;
	}
	public function reset() {
		return $this;
	}
}
class Soter_Cache_Redis implements Soter_Cache {
	private $config, $servers;
	public function __construct($config) {
		foreach ($config as $key => $node) {
			if (empty($node['slaves']) && !empty($node['master'])) {
				$config[$key]['slaves'][] = $node['master'];
			}
		}
		$this->config = $config;
	}
	private function &selectNode($key, $isRead) {
		$nodeIndex = sprintf("%u", crc32($key)) % count($this->config);
		if ($isRead) {
			$slaveIndex = array_rand($this->config[$nodeIndex]['slaves']);
			$serverKey = $nodeIndex . '-slaves-' . $slaveIndex;
			$config = $this->config[$nodeIndex]['slaves'][$slaveIndex];
		} else {
			$serverKey = $nodeIndex . '-master';
			$config = $this->config[$nodeIndex]['master'];
		}
		if (empty($this->servers[$serverKey])) {
			$this->servers[$serverKey] = $this->connect($config);
		}
		return $this->servers[$serverKey];
	}
	private function &connect($config) {
		$redis = new \Redis();
		if ($config['type'] == 'sock') {
			$redis->connect($config['sock']);
		} else {
			$redis->connect($config['host'], $config['port'], $config['timeout'], $config['retry']);
		}
		if (!is_null($config['password'])) {
			$redis->auth($config['password']);
		}
		if (!is_null($config['prefix'])) {
			if ($config['prefix']{strlen($config['prefix']) - 1} != ':') {
				$config['prefix'] .= ':';
			}
			$redis->setOption(\Redis::OPT_PREFIX, $config['prefix']);
		}
		$redis->select($config['db']);
		return $redis;
	}
	public function reset() {
		$this->servers = array();
		return $this;
	}
	public function clean() {
		$status = true;
		foreach ($this->config as $nodeIndex => $config) {
			$redis = $this->connect($config['master']);
			$status = $status && $redis->flushDB();
		}
		return $status;
	}
	public function delete($key) {
		$redis = $this->selectNode($key, false);
		return $redis->delete($key);
	}
	public function get($key) {
		$redis = $this->selectNode($key, true);
		if ($rawData = $redis->get($key)) {
			$data = @unserialize($rawData);
			return $data ? $data : $rawData;
		} else {
			return null;
		}
	}
	public function set($key, $value, $cacheTime = 0) {
		$redis = $this->selectNode($key, false);
		$value = serialize($value);
		if ($cacheTime) {
			return $redis->setex($key, $cacheTime, $value);
		} else {
			return $redis->set($key, $value);
		}
	}
	public function &instance($key = null, $isRead = true) {
		return $this->selectNode($key, $isRead);
	}
}
class Soter_Cache_Redis_Cluster implements Soter_Cache {
	private $config, $handle;
	public function __construct($config) {
		if (!is_null($config['prefix']) && ($config['prefix']{strlen($config['prefix']) - 1} != ':')) {
			$config['prefix'] .= ':';
		}
		$this->config = $config;
	}
	private function _init() {
		if (empty($this->handle)) {
			$this->handle = new \RedisCluster(null, $this->config['hosts'], $this->config['timeout'], $this->config['read_timeout'], $this->config['persistent']);
			if ($this->config['prefix']) {
				$this->handle->setOption(\RedisCluster::OPT_PREFIX, $this->config['prefix']);
			}
		}
	}
	public function reset() {
		$this->handle = null;
		return $this;
	}
	public function clean() {
		throw new \Soter_Exception_500('clean method not supported of Soter_Cache_Redis_Cluster ');
	}
	public function delete($key) {
		$this->_init();
		return $this->handle->del($key);
	}
	public function get($key) {
		$this->_init();
		if ($rawData = $this->handle->get($key)) {
			$data = @unserialize($rawData);
			return $data ? $data : $rawData;
		} else {
			return null;
		}
	}
	public function set($key, $value, $cacheTime = 0) {
		$this->_init();
		$value = serialize($value);
		if ($cacheTime) {
			return $this->handle->setex($key, $cacheTime, $value);
		} else {
			return $this->handle->set($key, $value);
		}
	}
	public function &instance($key = null, $isRead = true) {
		$this->_init();
		return $this->handle;
	}
}
class Soter_Generator extends Soter_Task {
	public function execute(Soter_CliArgs $args) {
		$config = \Sr::config();
		$name = $args->get('name');
		$type = $args->get('type');
		$force = $args->get('overwrite');
		if (empty($name)) {
			exit('name required , please use : --name=<Name>');
		}
		if (empty($type)) {
			exit('type required , please use : --type=<Type>');
		}
		$classesDir = $config->getPrimaryApplicationDir() . $config->getClassesDirName() . '/';
		$info = array(
		    'controller' => array(
			'dir' => $config->getControllerDirName(),
			'parentClass' => 'Soter_Controller',
			'methodName' => \Sr::config()->getMethodPrefix() . 'index()',
			'nameTip' => 'Controller'
		    ),
		    'business' => array(
			'dir' => $config->getBusinessDirName(),
			'parentClass' => 'Soter_Business',
			'methodName' => 'business()',
			'nameTip' => 'Business'
		    ),
		    'model' => array(
			'dir' => $config->getModelDirName(),
			'parentClass' => 'Soter_Model',
			'methodName' => 'model()',
			'nameTip' => 'Model'
		    ),
		    'task' => array(
			'dir' => $config->getTaskDirName(),
			'parentClass' => 'Soter_Task',
			'methodName' => 'execute(Soter_CliArgs $args)',
			'nameTip' => 'Task'
		    )
		);
		if (!\Sr::arrayKeyExists($type, $info)) {
			exit('[ Error ]' . "\n" . 'Type : [ ' . $type . ' ]');
		}
		$classname = $info[$type]['dir'] . '_' . $name;
		$file = $classesDir . str_replace('_', '/', $classname) . '.php';
		$method = $info[$type]['methodName'];
		$parentClass = $info[$type]['parentClass'];
		$tip = $info[$type]['nameTip'];
		if (file_exists($file)) {
			if ($force) {
				$this->writeFile($classname, $method, $parentClass, $file, $tip);
			} else {
				exit('[ Error ]' . "\n" . $tip . ' [ ' . $classname . ' ] already exists , ' . "{$file}\n" . 'you can use --overwrite to overwrite the file.');
			}
		} else {
			$this->writeFile($classname, $method, $parentClass, $file, $tip);
		}
	}
	private function writeFile($classname, $method, $parentClass, $file, $tip) {
		$dir = dirname($file);
		if (!is_dir($dir)) {
			mkdir($dir, 0755, true);
		}
		$code = "<?php\nclass  {$classname} extends {$parentClass} {\n	public function {$method} {\n		\n	}\n}";
		if (file_put_contents($file, $code)) {
			echo "[ Successfull ]\n{$tip} [ $classname ] created successfully \n" . $file;
		}
	}
}
class Soter_Generator_Mysql extends Soter_Task {
	public function execute(Soter_CliArgs $args) {
		$config = \Sr::config();
		$name = $args->get('name');
		$type = $args->get('type');
		$force = $args->get('overwrite');
		$table = $args->get('table');
		$dbGroup = $args->get('db');
		if (empty($name)) {
			exit('name required , please use : --name=<Name>');
		}
		if (empty($table)) {
			exit('table name required , please use : --table=<Table Name>');
		}
		if (empty($type)) {
			exit('type required , please use : --type=<Type>');
		}
		$columns = self::getTableFieldsInfo($table, $dbGroup);
		$primaryKey = '';
		$classesDir = $config->getPrimaryApplicationDir() . $config->getClassesDirName() . '/';
		$info = array(
		    'bean' => array(
			'dir' => $config->getBeanDirName(),
			'parentClass' => 'Soter_Bean',
			'nameTip' => 'Bean'
		    ),
		    'dao' => array(
			'dir' => $config->getDaoDirName(),
			'parentClass' => 'Soter_Dao',
			'nameTip' => 'Dao'
		    ),
		);
		if (!\Sr::arrayKeyExists($type, $info)) {
			exit('[ Error ]' . "\n" . 'Type : [ ' . $type . ' ]');
		}
		$classname = $info[$type]['dir'] . '_' . $name;
		$file = $classesDir . str_replace('_', '/', $classname) . '.php';
		$parentClass = $info[$type]['parentClass'];
		$tip = $info[$type]['nameTip'];
		$dir = dirname($file);
		if (!is_dir($dir)) {
			mkdir($dir, 0755, true);
		}
		if ($type == 'bean') {
			$methods = array();
			$fields = array();
			$fieldTemplate = "	//{comment}\n	private \${column0};";
			$methodTemplate = "	public function get{column}() {\n		return \$this->{column0};\n	}\n\n	public function set{column}(\${column1}) {\n		\$this->{column0} = \${column1};\n		return \$this;\n	}";
			foreach ($columns as $value) {
				$column = str_replace(' ', '', ucwords(str_replace('_', ' ', $value['name'])));
				$column0 = $value['name'];
				$column1 = lcfirst($column);
				$fields[] = str_replace(array('{column0}', '{comment}'), array($column0, $value['comment']), $fieldTemplate);
				$methods[] = str_replace(array('{column}', '{column0}', '{column1}'), array($column, $column0, $column1), $methodTemplate);
			}
			$code = "<?php\n\nclass {$classname} extends {$parentClass} {\n\n{fields}\n\n{methods}\n\n}";
			$code = str_replace(array('{fields}', '{methods}'), array(implode("\n\n", $fields), implode("\n\n", $methods)), $code);
		} else {
			$columnsString = '';
			$_columns = array();
			foreach ($columns as $value) {
				if ($value['primary']) {
					$primaryKey = $value['name'];
				}
				$_columns[] = '\'' . $value['name'] . "'//" . $value['comment'] . "\n				";
			}
			$columnsString = "array(\n				" . implode(',', $_columns) . ')';
			$code = "<?php\n\nclass {$classname} extends {$parentClass} {\n\n	public function getColumns() {\n		return {columns};\n	}\n\n	public function getPrimaryKey() {\n		return '{primaryKey}';\n	}\n\n	public function getTable() {\n		return '{table}';\n	}\n\n}\n";
			$code = str_replace(array('{columns}', '{primaryKey}', '{table}'), array($columnsString, $primaryKey, $table), $code);
		}
		if (file_exists($file)) {
			if ($force) {
				if (file_put_contents($file, $code)) {
					echo "[ Successfull ]\n{$tip} [ $classname ] created successfully \n" . $file;
				}
			} else {
				exit('[ Error ]' . "\n" . $tip . ' [ ' . $classname . ' ] already exists , ' . "{$file}\n" . 'you can use --overwrite to overwrite the file.');
			}
		} else {
			if (file_put_contents($file, $code)) {
				echo "[ Successfull ]\n{$tip} [ $classname ] created successfully \n" . $file;
			}
		}
	}
	private static function getTableFieldsInfo($tableName, $db) {
		if (!is_object($db)) {
			$db = \Sr::db($db);
		}
		if (strtolower($db->getDriverType()) != 'mysql') {
			throw new \Soter_Exception_500('getTableFieldsInfo() only for mysql database');
		}
		$info = array();
		$result = $db->execute('SHOW FULL COLUMNS FROM ' . $db->getTablePrefix() . $tableName)->rows();
		if ($result) {
			foreach ($result as $val) {
				$info[$val['Field']] = array(
				    'name' => $val['Field'],
				    'type' => $val['Type'],
				    'comment' => $val['Comment'] ? $val['Comment'] : $val['Field'],
				    'notnull' => $val['Null'] == 'NO' ? 1 : 0,
				    'default' => $val['Default'],
				    'primary' => (strtolower($val['Key']) == 'pri'),
				    'autoinc' => (strtolower($val['Extra']) == 'auto_increment'),
				);
			}
		}
		return $info;
	}
}
class Soter_Session_Redis extends Soter_Session {
	public function init() {
		ini_set('session.save_handler', 'redis');
		ini_set('session.save_path', $this->config['path']);
	}
}
class Soter_Session_Memcached extends Soter_Session {
	public function init() {
		ini_set('session.save_handler', 'memcached');
		ini_set('session.save_path', $this->config['path']);
	}
}
class Soter_Session_Memcache extends Soter_Session {
	public function init() {
		ini_set('session.save_handler', 'memcache');
		ini_set('session.save_path', $this->config['path']);
	}
}
class Soter_Session_Mongodb extends Soter_Session {
	private $__mongo_collection = NULL;
	private $__current_session = NULL;
	private $__mongo_conn = NULL;
	public function __construct($configFileName) {
		parent::__construct($configFileName);
		$cfg = \Sr::config()->getSessionConfig();
		$this->config['lifetime'] = $cfg['lifetime'];
	}
	public function connect() {
		if (is_object($this->__mongo_collection)) {
			return;
		}
		$connection_string = sprintf('mongodb://%s:%s', $this->config['host'], $this->config['port']);
		if ($this->config['user'] != null && $this->config['password'] != null) {
			$connection_string = sprintf('mongodb://%s:%s@%s:%s/%s', $this->config['user'], $this->config['password'], $this->config['host'], $this->config['port'], $this->config['database']);
		}
		$opts = array('connect' => true);
		if ($this->config['persistent'] && !empty($this->config['persistentId'])) {
			$opts['persist'] = $this->config['persistentId'];
		}
		if ($this->config['replicaSet']) {
			$opts['replicaSet'] = $this->config['replicaSet'];
		}
		$class = '\MongoClient';
		if (!class_exists($class)) {
			$class = '\Mongo';
		}
		$this->__mongo_conn = $object_conn = new $class($connection_string, $opts);
		$object_mongo = $object_conn->{$this->config['database']};
		$this->__mongo_collection = $object_mongo->{$this->config['collection']};
		if ($this->__mongo_collection == NULL) {
			throw new \Soter_Exception_500('can not connect to mongodb server');
		}
	}
	public function init() {
		session_set_save_handler(array(&$this, 'open'), array(&$this, 'close'), array(&$this, 'read'), array(&$this, 'write'), array(&$this, 'destroy'), array(&$this, 'gc'));
	}
	public function open($session_path, $session_name) {
		$this->connect();
		return true;
	}
	public function close() {
		$this->__mongo_conn->close();
		return true;
	}
	public function read($session_id) {
		$result = NULL;
		$ret = '';
		$expiry = time();
		$query['_id'] = $session_id;
		$query['expiry'] = array('$gte' => $expiry);
		$result = $this->__mongo_collection->findone($query);
		if ($result) {
			$this->__current_session = $result;
			$result['expiry'] = time() + $this->config['lifetime'];
			$this->__mongo_collection->update(array("_id" => $session_id), $result);
			$ret = $result['data'];
		}
		return $ret;
	}
	public function write($session_id, $data) {
		$result = true;
		$expiry = time() + $this->config['lifetime'];
		$session_data = array();
		if (empty($this->__current_session)) {
			$session_id = $session_id;
			$session_data['_id'] = $session_id;
			$session_data['data'] = $data;
			$session_data['expiry'] = $expiry;
		} else {
			$session_data = (array) $this->__current_session;
			$session_data['data'] = $data;
			$session_data['expiry'] = $expiry;
		}
		$query['_id'] = $session_id;
		$record = $this->__mongo_collection->findOne($query);
		if ($record == null) {
			$this->__mongo_collection->insert($session_data);
		} else {
			$record['data'] = $data;
			$record['expiry'] = $expiry;
			$this->__mongo_collection->save($record);
		}
		return true;
	}
	public function destroy($session_id) {
		unset($_SESSION);
		$query['_id'] = $session_id;
		$this->__mongo_collection->remove($query);
		return true;
	}
	public function gc($max = 0) {
		$query = array();
		$query['expiry'] = array(':lt' => time());
		$this->__mongo_collection->remove($query, array('justOne' => false));
		return true;
	}
}
/**
 * @property Soter_Database_ActiveRecord $dbConnection Description
 */
class Soter_Session_Mysql extends Soter_Session {
	protected $dbConnection;
	protected $dbTable;
	public function __construct($configFileName) {
		parent::__construct($configFileName);
		$cfg = \Sr::config()->getSessionConfig();
		$this->config['lifetime'] = $cfg['lifetime'];
	}
	public function init() {
		session_set_save_handler(array($this, 'open'), array($this, 'close'), array($this, 'read'), array($this, 'write'), array($this, 'destroy'), array($this, 'gc'));
	}
	public function connect() {
		$this->dbTable = $this->config['table'];
		if ($this->config['group']) {
			$this->dbConnection = \Sr::db($this->config['group']);
		} else {
			$dbConfig = \Soter_Database::getDefaultConfig();
			$dbConfig['database'] = $this->config['database'];
			$dbConfig['tablePrefix'] = $this->config['table_prefix'];
			$dbConfig['masters']['master01']['hostname'] = $this->config['hostname'];
			$dbConfig['masters']['master01']['port'] = $this->config['port'];
			$dbConfig['masters']['master01']['username'] = $this->config['username'];
			$dbConfig['masters']['master01']['password'] = $this->config['password'];
			$this->dbConnection = \Sr::db($dbConfig);
		}
	}
	public function open($save_path, $session_name) {
		if (!is_object($this->dbConnection)) {
			$this->connect();
		}
		return TRUE;
	}
	public function close() {
		$this->dbConnection->close();
		return true;
	}
	public function read($id) {
		$result = $this->dbConnection->from($this->dbTable)->where(array('id' => $id))->execute();
		if ($result->total()) {
			$record = $result->row();
			$where['id'] = $id;
			$data['timestamp'] = time() + intval($this->config['lifetime']);
			$this->dbConnection->update($this->dbTable, $data, $where)->execute();
			return $record['data'];
		} else {
			return false;
		}
		return true;
	}
	public function write($id, $sessionData) {
		$data['id'] = $id;
		$data['data'] = $sessionData;
		$data['timestamp'] = time() + intval($this->config['lifetime']);
		$this->dbConnection->replace($this->dbTable, $data);
		return $this->dbConnection->execute() > 0;
	}
	public function destroy($id) {
		unset($_SESSION);
		return $this->dbConnection->delete($this->dbTable, array('id' => $id))->execute() > 0;
	}
	public function gc($max = 0) {
		return $this->dbConnection->delete($this->dbTable, array('timestamp <' => time()))->execute() > 0;
	}
}
