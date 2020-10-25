<?php
/**
 * 基本类
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf;

use Yesf\Swoole;
use Yesf\Event\Internal;
use Yesf\Config\ConfigInterface;
use Yesf\Config\Adapter\Arr;
use Yesf\Exception\StartException;
use Yesf\Exception\NotFoundException;
use Yesf\Exception\RequirementException;

if (!defined('YESF_PATH')) {
	define('YESF_PATH', __DIR__ . '/');
}

set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext) {
	throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
}, E_WARNING | E_USER_ERROR | E_USER_WARNING | E_DEPRECATED | E_USER_DEPRECATED);

class Yesf {
	const CONF_ENV = 1;
	const CONF_PROJECT = 2;
	const CONF_SERVER = 3;
	//单例化
	protected static $instance = null;
	//运行环境，需要与配置文件中同名
	protected $environment = 'product';
	//配置
	protected static $config_project = null;
	protected static $config_project_hash = '';
	protected static $config_server = null;
	protected $config = null;
	protected $config_raw = null;
	/**
	 * 获取单例类
	 * 
	 * @access public
	 * @return object(Yesf)
	 */
	public static function app(): Yesf {
		if (self::$instance === null) {
			throw new StartException('Yesf have not been construct yet');
		}
		return self::$instance;
	}
	/**
	 * 实例化
	 * 
	 * @access public
	 * @param string/array/config $config 配置
	 */
	public function __construct() {
		if (self::$instance !== null) {
			throw new StartException('Yesf already constructed');
		}
		self::$instance = $this;
		//swoole检查
		if (!extension_loaded('swoole')) {
			throw new RequirementException('Extension "Swoole" is required');
		}
		if (version_compare(SWOOLE_VERSION, '4.0.0', '<')) {
			throw new RequirementException('Yesf require Swoole 4.0 or later');
		}
		//环境
		if (defined('APP_ENV')) {
			$this->environment = APP_ENV;
		}
		if (!defined('APP_PATH')) {
			throw new StartException('You must define APP_PATH before initialize Yesf');
		}
		//配置检查
		if (!is_file(APP_PATH . 'Config/Project.php')) {
			throw new NotFoundException('Project configure file not found');
		}
		if (!is_file(APP_PATH . 'Config/Server.php')) {
			throw new NotFoundException('Server configure file not found');
		}
		//其他各项配置
		self::$config_server = new Arr(require(APP_PATH . 'Config/Server.php'), '');
		self::loadProjectConfig();
		// Check app namespace
		$namespace = self::$config_project->get('namespace');
		if ('\\' !== $namespace[strlen($namespace) - 1]) {
			throw new StartException("A non-empty PSR-4 prefix must end with a namespace separator.");
		}
		Internal::onCreate();
		//编码相关
		if (function_exists('mb_internal_encoding')) {
			mb_internal_encoding(self::$config_project->get('charset'));
		}
		if (!defined('YESF_UNIT')) {
			Swoole::init();
		}
	}
	/**
	 * 将部分变量对外暴露，方便使用
	 * 
	 * @access public
	 */
	public function setEnvConfig($raw) {
		$this->config_raw = $raw;
	}
	public function loadEnvConfig() {
		if ($this->config_raw instanceof ConfigInterface) {
			$this->config = $this->config_raw;
		} elseif ($this->config_raw instanceof \Closure) {
			$config = $this->config_raw();
			if (!$config instanceof ConfigInterface) {
				throw new NotFoundException('Config can not be recognised');
			}
			$this->config = $config;
		} elseif (is_string($this->config_raw) && is_file($this->config_raw)) {
			$this->config = Arr::fromIniFile($this->config_raw);
		} elseif (is_array($this->config_raw)) {
			$this->config = new Arr($this->config_raw);
		} else {
			throw new NotFoundException('Config can not be recognised');
		}
	}
	public static function loadProjectConfig() {
		$hash = md5_file(APP_PATH . 'Config/Project.php');
		if (self::$config_project_hash === $hash) {
			return;
		}
		self::$config_project_hash = $hash;
		self::$config_project = new Arr(require(APP_PATH . 'Config/Project.php'), '');
	}
	public function getConfig($key = null, $type = self::CONF_ENV, $default = null) {
		switch ($type) {
			case self::CONF_ENV:
				$config = $this->config;
				break;
			case self::CONF_PROJECT:
				$config = self::$config_project;
				break;
			case self::CONF_SERVER:
				$config = self::$config_server;
				break;
		}
		if ($key === null) {
			return $config;
		} else {
			return $config->get($key, $default);
		}
	}
	public function setEnvironment($env) {
		$this->environment = $env;
	}
	public function getEnvironment() {
		return $this->environment;
	}
	/**
	 * 初始化完成，开始运行
	 * 
	 * @access public
	 */
	public function run($config = null) {
		if ($config !== null) $this->setEnvConfig($config);
		Swoole::start();
	}
}