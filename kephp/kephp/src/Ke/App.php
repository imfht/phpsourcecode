<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke;

if (!class_exists('Composer\\Autoload\\ClassLoader', false)) {
	if (!defined('KE_VER'))
		require __DIR__ . '/Base.php';

	if (!class_exists(DirectoryRegistry::class))
		require __DIR__ . '/DirectoryRegistry.php';

	if (!class_exists(Loader::class))
		require __DIR__ . '/Loader.php';
}

use Exception as PhpException;

class App
{

	/** @var array 已知的服务器名 */
	private static $knownServers = [
		'' => KE_DEV,
		'0.0.0.0' => KE_DEV,
		'localhost' => KE_DEV,
		'127.0.0.1' => KE_DEV,
	];

	/** @var App */
	private static $app = null;

	private $isInit = false;

	private $root = null;

	private $loader = null;

	/** @var string 项目的名称 */
	protected $name = null;

	/** @var string 项目的基础Hash */
	protected $salt = null;

	/** @var string 区域语言习惯 */
	protected $locale = 'en_US';

	/** @var string 默认时区 */
	protected $timezone = 'Asia/Shanghai';

	/** @var string 编码 */
	protected $encoding = 'UTF-8';

	/**
	 * 编码顺序，值类型应该数组格式，或者以逗号分隔的字符串类型
	 *
	 * 'GBK,GB2312,CP936'
	 * ['GBK', 'GB2312', 'CP936']
	 *
	 * @var string|array
	 */
	protected $encodingOrder = ['GBK', 'GB2312'];

	/** @var string http的路径前缀 */
	protected $httpBase = null;

	protected $isInitHttpBase = false;

	/** @var bool 是否开启了HTTP REWRITE */
	protected $httpRewrite = true;

	/** @var string http的验证字段 */
	protected $httpSecurityField = null;

	/** @var string http验证字段的内容加密的hash */
	protected $httpSecuritySalt = null;

	protected $httpSecuritySessionField = null;

	/** @var array 声明SERVER_NAME所对应的应用程序运行环境 */
	protected $servers = [];

	protected $helpers = [];

	/**
	 * 目录的别名，这里的目录生成，是以KE_APP_ROOT为基础展开的
	 *
	 * @var array 目录的别名
	 */
	protected $aliases = [];

	/** @var array 绝对路径的文件目录存放 */
	protected $dirs = [];

	/** @var MimeType */
	protected $mime = null;

	/**
	 * @return $this
	 * @throws PhpException
	 */
	public static function getApp()
	{
		if (!isset(self::$app))
			throw new PhpException('App instance is not created!');
		return self::$app;
	}

	final public function __construct(string $root = null, array $dirs = null)
	{
		if (isset(self::$app))
			throw new PhpException('Create App repeated instances!');
		self::$app = $this;

		// 检查根目录
		if (($this->root = real_dir($root)) === false)
			throw new PhpException('App directory (root) does not exist or is not a directory!');

		// 增加一个接口
		$this->onBootstrap();

		/** App的根目录的绝对路径 */
		define('KE_APP_ROOT', $this->root);
		/** App的目录名 */
		define('KE_APP_DIR', basename($this->root));

		// 后注册，让后继承的App类，可以以声明属性的方式来添加
		if (!isset($this->aliases['web']))
			$this->aliases['web'] = 'public';

		/** @var string $kephp kephp的根目录 */
		$this->dirs['kephp'] = dirname(__DIR__);

		if (!empty($dirs))
			$this->setDirs($dirs);

		// CLI模式加载特定的环境配置文件
		if (KE_APP_MODE === KE_CLI) {
			// 先尝试加载环境配置文件，这个文件以后会扩展成为json格式，以装载更多的信息
			$envFile = $this->root . '/env';
			if (is_file($envFile) && is_readable($envFile)) {
				$_SERVER['SERVER_NAME'] = trim(file_get_contents($envFile));
			} else {
				$_SERVER['SERVER_NAME'] = 'localhost';
			}
		}

		// 绑定servers
		$this->servers += self::$knownServers;

		// 匹配当前的运行环境
		$env = $this->detectEnv();
		// 不是开发模式或者测试模式，就必然是发布模式，确保在未知的模式下，返回发布模式
		if ($env !== KE_DEV && $env !== KE_TEST)
			$env = KE_PRO;
		/** App当前的运行环境 */
		define('KE_APP_ENV', $env);

		/** @var string $appSrc App的src目录 */
		$appSrc = $this->path('src');

		// 项目的基础的类、命名空间和命名空间对应的路径
		$appClass = static::class;
		$appNs = null;
		$appNsPath = $appSrc;
		if ($appClass !== __CLASS__) {
			list($appNs) = parse_class($appClass);
			if (!empty($appNs)) {
				$appNsPath .= DS . $appNs;
			}
			if (!KE_IS_WIN)
				$appNsPath = str_replace('\\', '/', $appNsPath);
		}

		/** 记录下全局的App的类名称 */
		define('KE_APP_CLASS', $appClass);
		/** 当前的App类名的namespace */
		define('KE_APP_NS', $appNs);
		/** 当前App类的Namespace指向的绝对路径 */
		define('KE_APP_NS_PATH', $appNsPath);

		$this->dirs['appNs'] = $appNsPath;

		$this->loader = new Loader([
			'dirs' => [
				'appSrc' => [$appSrc, 100, Loader::CLS],
				'appHelper' => ["{$appNsPath}/Helper", 100, Loader::HELPER],
				'keHelper' => ["{$this->dirs['kephp']}/Ke/Helper", 1000, Loader::HELPER],
			],
			'classes' => import("{$this->dirs['kephp']}/classes.php"),
			'prepend' => true,
		]);
		$this->loader->start();
		if (!empty($this->helpers))
			$this->loader->loadHelper(...$this->helpers);


		$this->onConstruct($this->loader);
	}

	protected function onConstruct(Loader $loader)
	{
	}

	protected function onBootstrap()
	{
	}

	/**
	 * 获取请求的相对（Document Root）的脚本路径
	 *
	 * @return string
	 */
	public function getDocumentScriptPath()
	{
		if (PHP_SAPI === 'cli-server') {
			$scriptFile = $_SERVER['SCRIPT_FILENAME'];
			if (strpos($scriptFile, '/') !== 0)
				$scriptFile = '/' . $scriptFile;
			return $scriptFile;
		}
		if (!empty($_SERVER['PATH_INFO']))
			return $_SERVER['PATH_INFO'];
		return $_SERVER['SCRIPT_NAME'];
	}

	public function initHttpBase($refresh = false)
	{
		if ($this->isInitHttpBase && !$refresh) return $this->httpBase;
		// Uri准备
		Uri::prepare();
		if (empty($this->httpBase)) {
			$target = dirname($this->getDocumentScriptPath());
			if ($target === '\\')
				$target = '/';
			$this->httpBase = compare_path(KE_REQUEST_PATH, $target, KE_DS_UNIX);
		} else if ($this->httpBase !== '/') {
			$this->httpBase = purge_path($this->httpBase, KE_PATH_DOT_REMOVE ^ KE_PATH_LEFT_TRIM, KE_DS_UNIX);
		}
		// 上面的过滤，无论如何，过滤出来的httpBase都为没有首位的/的路径，如:path/dir/dir
		if (empty($this->httpBase))
			$this->httpBase = '/';
		else if ($this->httpBase !== '/')
			$this->httpBase = '/' . $this->httpBase . '/';
		// 如果不指定重写，则httpBase应该是基于一个php文件为基础的
		if (!$this->httpRewrite)
			$this->httpBase .= KE_SCRIPT_FILE;

		if (!defined('KE_HTTP_BASE')) {
			define('KE_HTTP_BASE', $this->httpBase);
			define('KE_HTTP_REWRITE', (bool)$this->httpRewrite);
		}
		$this->isInitHttpBase = true;

		return $this->httpBase;
	}

	public function isInitHttpBase()
	{
		return $this->isInitHttpBase;
	}

	final public function init()
	{
		if ($this->isInit)
			return $this;

		$env = KE_APP_ENV;
		// 调整初始化 HttpBase 的执行顺序，不在 App::__construct 时执行
		$this->initHttpBase();

		// 加载配置
		import([
			"{$this->root}/config/common.php",
			"{$this->root}/config/{$env}.php",
		]);

		if (KE_APP_MODE === KE_WEB) {
			$this->httpRewrite = (bool)$this->httpRewrite;
		}

		/////////////////////////////////////////////////////////////////////////////
		// p2：填充当前的APP实例的数据
		/////////////////////////////////////////////////////////////////////////////
		// 初始化项目的名称 => 不应为空，也必须是一个字符串
		if (empty($this->name) || !is_string($this->name))
			$this->name = KE_APP_DIR;

		// 一个App的完整摘要
		$summary = sprintf('%s(%s,%s,%s)', $this->name, KE_APP_ENV, KE_REQUEST_HOST, $this->root);

		// 项目的hash，基于完整摘要生成，而非基于用户设置的项目名称
		// hash，主要用于服务器缓存识别不同的项目时使用
		// 比如memcached，key为user.10，而这个项目的存储则应该是：$flag.user.10，来避免项目和项目之间的数据混串
		$hash = hash('crc32b', $summary);

		// 真正用于显示的项目名称，包含项目名称、环境、hash
		$this->name = sprintf('%s(%s:%s)', $this->name, KE_APP_ENV, $hash);

		// 项目的基本加密混淆码 => 不应为空，也必须是一个字符串，且必须不小于32长度
		if (empty($this->salt) || !is_string($this->salt) || strlen($this->salt) < 32)
			$salt = $summary;
		else
			$salt = $this->salt;

		define('KE_APP_NAME', $this->name);
		define('KE_APP_HASH', $hash);
		define('KE_APP_SALT', hash('sha512', $salt, true));
		// 敏感数据还是清空为妙
		$this->salt = null;

		// http验证字段，如果没指定，就只好使用一个统一的了
		if (empty($this->httpSecurityField) || !is_string($this->httpSecurityField))
			$this->httpSecurityField = 'ke_http';

		if (empty($this->httpSecuritySessionField) || !is_string($this->httpSecuritySessionField))
			$this->httpSecuritySessionField = 'ke_security_reference';

		// http验证字段的加密混淆码
		if (empty($this->httpSecuritySalt) || !is_string($this->httpSecuritySalt))
			$this->httpSecuritySalt = "{$this->name}:{$this->httpSecurityField}";

		$this->httpSecuritySalt = $this->hash($this->httpSecuritySalt);

		define('KE_HTTP_SECURITY_FIELD', $this->httpSecurityField);
		define('KE_HTTP_SECURITY_SALT', $this->httpSecuritySalt);
		define('KE_HTTP_SECURITY_SESS_FIELD', $this->httpSecuritySessionField);
		// 敏感数据还是清空为妙
		$this->httpSecuritySalt = null;

		// 检查httpCharset
		if (empty($this->encoding) || false === @mb_encoding_aliases($this->encoding))
			$this->encoding = 'UTF-8';

		if (!empty($this->encodingOrder)) {
			if (is_string($this->encodingOrder))
				$this->encodingOrder = explode(',', $this->encodingOrder);
			if (is_array($this->encodingOrder)) {
				$list = ['ASCII'];
				foreach ($this->encodingOrder as $encoding) {
					$encoding = strtoupper(trim($encoding));
					if (empty($encoding) || $encoding === 'ASCII' || $encoding === $this->encoding)
						continue;
					$list[] = $encoding;
				}
				$list[] = $this->encoding;
				mb_detect_order($list);
			}
		}

		// 时区
		if (empty($this->timezone) || false === @date_default_timezone_set($this->timezone)) {
			$this->timezone = 'Asia/Shanghai';
			date_default_timezone_set($this->timezone);
		}

		define('KE_APP_TIMEZONE', $this->timezone);
		define('KE_APP_ENCODING', $this->encoding);

		// 系统的配置
		ini_set('default_charset', KE_APP_ENCODING);
		ini_set('default_mimetype', 'text/html');
		mb_internal_encoding(KE_APP_ENCODING);
		mb_http_output(KE_APP_ENCODING);

		$this->isInit = true;
		$this->onInit();

		call_user_func([$this, 'on' . KE_APP_ENV]);

		register_shutdown_function(function () {
			$this->onExiting();
		});

		return $this;
	}

	protected function onInit()
	{
	}

	public function isInit()
	{
		return $this->isInit;
	}

	/**
	 * 开发环境的接口
	 */
	protected function onDevelopment()
	{
	}

	/**
	 * 测试环境的接口
	 */
	protected function onTest()
	{
	}

	/**
	 * 产品环境的接口
	 */
	protected function onProduction()
	{
	}

	protected function onExiting()
	{
	}

	public function hash(string $content, string $salt = KE_APP_HASH): string
	{
		return hash('sha512', $content . $salt, false);
	}

	/**
	 * 获取服务器名称
	 *
	 * @return string
	 */
	public function getServerName()
	{
		return $_SERVER['SERVER_NAME'] ?? 'localhost';
	}

	/**
	 * 识别当前App的运行环境，如果不做匹配，请确保该函数返回的结果为空。
	 *
	 * @return null
	 */
	public function detectEnv()
	{
		$serverName = $this->getServerName();
		if (isset($this->servers[$serverName]))
			return $this->servers[$serverName];
		return KE_PRO;
	}

	public function setDirs(array $dirs)
	{
		foreach ($dirs as $name => $dir) {
			// 不为空，必须是字符串，不允许'kephp'和'root'两个关键字的写入
			if (empty($name) || !is_string($name) || $name === 'kephp' || $name === 'root')
				continue;
			// dir如果为：null false，表示删除掉这个目录
			if ($dir === null || $dir === false) {
				unset($this->dirs[$name]);
				continue;
			} else if (empty($dir) || !is_string($dir)) {
				// 目录也必须是字符串类型
				// todo: 以后要增加对Object和Array类型的识别
				continue;
			}
			// 这里其实有些尴尬，realpath，是基于当前执行的脚本为基础入口的，所以这里可能还有一些问题
			if (($real = real_dir($dir)) !== false)
				$this->dirs[$name] = $real;
			else
				$this->aliases[$name] = $dir;
		}
		return $this;
	}

	public function getDirs(): array
	{
		return $this->dirs + $this->aliases;
	}

	public function path(string $name = null, string $path = null, string $ext = null)
	{
		$result = false;
		if (empty($name) || $name === 'root')
			$result = $this->root;
		else if (isset($this->dirs[$name]))
			$result = $this->dirs[$name];
		else {
			$this->dirs[$name] =
			$result = $this->root . DS . (empty($this->aliases[$name]) ? $name : $this->aliases[$name]);
		}
		if (!empty($path)) {
			if (!empty($ext))
				$path = ext($path, $ext);
			$result .= DS . $path;
		}
		if (!KE_IS_WIN) {
			$result = str_replace('\\', '/', $result);
		}
		return $result;
	}

	public function __call(string $name, array $args)
	{
		return $this->path($name, ...$args);
	}

	public function getLoader()
	{
		return $this->loader;
	}

	public function getMime()
	{
		if (!isset($this->mime) || !($this->mime instanceof MimeType))
			$this->mime = new MimeType();
		return $this->mime;
	}
}
