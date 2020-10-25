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

use voku\helper\AntiXSS;

/**
 * Uri类
 *
 * 替换原来的实现，原来的实现setData太过复杂，调试不易。
 *
 * 部分接口遵照PSR-7规范，但不完全遵守PSR-7规范的要求。
 *
 * 本类经过精心的调整，可支持任意的继承扩展，如`/Ke/Web/Http`则是继承自该类而实现
 *
 * @package Ke\Core
 * @link    https://github.com/php-fig/http-message/blob/master/src/UriInterface.php
 * @link    http://tools.ietf.org/html/rfc3986
 * @property string $scheme
 * @property string $host
 * @property string $user
 * @property string $pass
 * @property int    $port
 * @property string $path
 * @property array  $query
 * @property string $fragment
 * @property string $hostPort
 * @property string $authority
 * @property string $userInfo
 * @property string $queryString
 * @property string $uri
 */
class Uri
{

	const QUERY_NUMERIC_PREFIX = null;
	const QUERY_SEPARATOR = '&';
	const QUERY_ENCODE_TYPE = PHP_QUERY_RFC3986;

	private static $isPrepare = false;

	/** @var array 存放已经解析过的路径 */
	private static $purgePaths = [];

	private static $filterPaths = [];

	private static $currentUri = null;

	/** @var AntiXSS */
	private static $antiXSSHelper = null;

	/** @var array 已知的协议、端口号 */
	private static $stdPorts = [
		'http'  => 80,
		'https' => 443,
		'ftp'   => 21,
		'ssh'   => 22,
		'sftp'  => 22,
	];

	/** @var array 数据容器 */
	protected $data = [
		'scheme'   => null,
		'host'     => null,
		'user'     => null,
		'pass'     => null,
		'port'     => null,
		'path'     => null,
		'query'    => null,
		'fragment' => null,
	];

	/** @var array */
	protected $queryData = [];

	/** @var bool 是否忽略路径末尾的 / */
	private $ignoreEndSlash = true;

	protected $isHideAuthority = false;

//	protected $cleanEndSlash = false;

	private $filterPath = '';

	private $isChangePath = false;

	protected $queryNumericPrefix = self::QUERY_NUMERIC_PREFIX;

	protected $querySeparator = self::QUERY_SEPARATOR;

	protected $queryEncodeType = self::QUERY_ENCODE_TYPE;

	/**
	 * 检查端口号是否为相关协议的标准端口
	 *
	 * ```php
	 * Uri::isStdPort(80, 'http'); // true
	 * Uri::isStdPort(8080, 'http'); // false
	 * ```
	 *
	 * @param string $scheme scheme，必须是小写的格式
	 * @param int    $port   端口号
	 *
	 * @return bool
	 */
	public static function isStdPort($port, $scheme = null): bool
	{
		return !empty($scheme) && isset(self::$stdPorts[$scheme]) && self::$stdPorts[$scheme] === intval($port);
	}

	/**
	 * 获取 AntiXSS 辅助器的实例对象
	 *
	 * @return AntiXSS
	 */
	public static function getAntiXSSHelper()
	{
		if (!isset(self::$antiXSSHelper)) {
			self::$antiXSSHelper = new AntiXSS();
		}
		return self::$antiXSSHelper;
	}

	/**
	 * 过滤路径，这个方法其实是简化版的`purge_path`
	 *
	 * 已经过滤过的路径，会放入静态变量中临时保存，以确保一次会话中可多次重复使用。
	 *
	 * @param string     $path           要过滤的路径
	 * @param bool       $ignoreEndSlash 是否忽略处理末尾的 /， true 如果不是文件后缀格式的，强制补充末尾的 /，默认为 false
	 * @param array|null $excludes       需要过滤掉的路径片段，key -> value 存放。
	 *
	 * @return string
	 */
	public static function filterPath($path, bool $ignoreEndSlash = false, array $excludes = null): string
	{
		if (empty($path))
			return '';
		// 如果存在 % 符号，则优先尝试解码
		// @todo 这里可能会出现一些问题，需要监控一下
		if (strpos($path, '%') !== false) $path = urldecode($path);
		// 去掉无效的路径分隔符
		if (strpos($path, '\\') !== false) $path = str_replace('\\', '/', $path);

		$isAbsolute = false;
		$split = explode('/', $path);
		$segments = [];
		foreach ($split as $index => $segment) {
			if (strlen($segment) <= 0 || $segment === KE_DS_UNIX || $segment === KE_DS_WIN) {
				if ($index === 0)
					$isAbsolute = true;
				continue;
			}
			if ($segment === '.')
				continue;
			$segment = preg_replace('#^\.{2,}#', '..', $segment);
			$segment = urldecode($segment);
			if (!empty($excludes) && array_search($segment, $excludes, true) !== false)
				continue;
			$segments[] = $segment;
		}
		if (!$ignoreEndSlash) {
			$count = count($segments);
			if ($count > 0) {
				$last = $segments[$count - 1];
				if (!preg_match('#[^.]+\.[^.]+#', $last)) {
					$segments[] = '';
				}
			}
		}
		$result = implode('/', $segments);
		if ($isAbsolute)
			$result = '/' . $result;
		return $result;
	}

	public static function filterQuery(array $query = [], $isXssClean = false)
	{
		if ($isXssClean && !empty($query)) {
			$query = static::getAntiXSSHelper()->xss_clean($query);
		}
		return $query;
	}

	/**
	 * 全局的Uri预备函数
	 *
	 * 将当前的请求（包括执行的脚本），解析为一个合乎规范的uri。并将uri拆分成几个常量来保存：
	 *
	 * KE_REQUEST_SCHEME => 当前请求的协议（CLI模式下，为cli）
	 * KE_REQUEST_HOST   => 当前请求的主机名，如果端口为非标准端口，该常量将包含端口号（CLI模式，则根据env文件）
	 * KE_REQUEST_URI    => 当前请求的URI，这个URI表示为本地主机的URI，即不包含scheme和host的部分，包含queryString
	 * KE_REQUEST_PATH   => URI的路径部分（排除queryString）
	 *
	 * 在cli模式，假定执行文件为：php /var/www/kephp/tests/hello.php
	 *
	 * 在当前环境下，已经定义了项目（App）的根目录为：/var/www/kephp
	 *
	 * 则当前的cli模式下，完整的URI为：cli://localhost/kephp/tests/hello.php
	 *
	 * 相应的，常量内容如下：
	 * KE_REQUEST_SCHEME => cli
	 * KE_REQUEST_HOST   => localhost
	 * KE_REQUEST_URI    => /kephp/tests/hello.php
	 * KE_REQUEST_PATH   => /kephp/tests/hello.php
	 *
	 * cli模式下，KE_REQUEST_PATH的第一段，代表的就是这个项目的根目录，在执行替换的时候，可以将第一段（/kephp）替换为KE_APP常量的内容
	 *
	 * cli模式下，queryString即为$_SERVER['argv']
	 *
	 * @return bool
	 */
	public static function prepare(): bool
	{
		if (self::$isPrepare === true)
			return false;
		self::$isPrepare = true;
		$ptcVer = null;
		$query = [];
		if (PHP_SAPI === 'cli') {
			$_SERVER['REQUEST_SCHEME'] = 'cli';
			if (empty($_SERVER['SERVER_NAME']))
				$_SERVER['SERVER_NAME'] = 'localhost';
			if (empty($_SERVER['HTTP_HOST']))
				$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'];
			// @todo cli的queryString即为执行时的参数，具体为：$_SERVER['argv'] 1 .. n => $query，后续会增加
			$path = real_file(KE_SCRIPT_PATH);
			// 将执行的脚本路径写入到uri中
			$_SERVER['REQUEST_URI'] = $path;
		} else {
			// http 协议
			$ptcStr = $_SERVER['HTTP_X_CLIENT_PROTO_VER'] ?? $_SERVER['SERVER_PROTOCOL'] ?? '';
			$ptcVer = empty($ptcStr) ? '1.1' : substr($ptcStr, strpos($ptcStr, '/') + 1);
			// http scheme
			// 反向代理的https协议传递
			$httpScheme = strtolower($_SERVER['HTTP_X_CLIENT_PROTO'] ?? $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? $_SERVER['HTTP_X_SCHEME'] ?? $_SERVER['REQUEST_SCHEME'] ?? 'http');
			if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
				$httpScheme = 'https';
			$_SERVER['REQUEST_SCHEME'] = $httpScheme;
			// http host
			if (!isset($_SERVER['HTTP_HOST'])) {
				$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'];
				if (!static::isStdPort((int)$_SERVER['SERVER_PORT'], $_SERVER['REQUEST_SCHEME']))
					$_SERVER['HTTP_HOST'] .= ':' . $_SERVER['SERVER_PORT'];
			} else {
				$_SERVER['HTTP_HOST'] = strtolower($_SERVER['HTTP_HOST']);
			}
			// 过滤 uri
			$uri = '/' . trim($_SERVER['REQUEST_URI'], '/ \\');
			$parse = parse_url($uri);
			if (empty($parse['path']))
				$parse['path'] = '';
			elseif ($parse['path'] !== '/')
				$parse['path'] = static::filterPath($parse['path']);
			$path = $parse['path'];
			if (!empty($parse['query'])) {
				parse_str($parse['query'], $query);
				$query = static::filterQuery($query, !defined('KE_WEB_CLEAN_XSS') || KE_WEB_CLEAN_XSS);
			}
			$_SERVER['QUERY_STRING'] = empty($query) ? '' : http_build_query($query, self::QUERY_NUMERIC_PREFIX, self::QUERY_SEPARATOR, self::QUERY_ENCODE_TYPE);
			$_SERVER['REQUEST_URI'] = $path . (empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING']);
			// 这个只在HTTP模式有用，这个SCRIPT_NAME可能会出现/aa///bb，但一定不会出现/../aabb/./
			// 所以这里只亮小路径过滤，而不用大路径过滤方法
			// 而且他也是HTTP的路径，也符合
			$_SERVER['SCRIPT_NAME'] = static::filterPath($_SERVER['SCRIPT_NAME']);
		}

		self::$purgePaths[$path] = 1;
		define('KE_REQUEST_SCHEME', $_SERVER['REQUEST_SCHEME']);
		define('KE_REQUEST_HOST', $_SERVER['HTTP_HOST']);
		define('KE_REQUEST_URI', $_SERVER['REQUEST_URI']);
		define('KE_REQUEST_PATH', $path);
		define('KE_PROTOCOL_VER', $ptcVer);

		return true;
	}

	/**
	 * 返回当前PHP请求的URI实例。
	 *
	 * @return $this
	 */
	public static function current()
	{
		if (!isset(self::$currentUri)) {
			self::$currentUri = new static([
				'scheme' => KE_REQUEST_SCHEME,
				'host'   => KE_REQUEST_HOST,
				'uri'    => KE_REQUEST_URI,
			]);
		}
		return self::$currentUri;
	}

	/**
	 * Uri 构建函数
	 *
	 * 构建函数允许传入多种格式的参数：
	 *
	 * * 字符串，会执行相应的url解析方法
	 * * 数组，`[ 'scheme' => '...', 'path' => '...', 'uri' => '...', ... ]`
	 * * 对象，会将对象转为数组
	 *
	 * @param null|array|object|string $data
	 * @param bool                     $ignoreEndSlash
	 */
	public function __construct($data = null, bool $ignoreEndSlash = false)
	{
		$this->ignoreEndSlash = $ignoreEndSlash;
		if (self::$isPrepare === false)
			static::prepare();
		if (isset($data))
			$this->setData($data);
	}

	/**
	 * 设置Uri的数据
	 *
	 * $data为字符串，表示为path
	 * ```php
	 * $uri->setData('hello');
	 * ```
	 *
	 * 等价于
	 * ```php
	 * $uri->setData(['path' => 'hello']);
	 * ```
	 *
	 * 关于路径的拼接处理
	 * ```php
	 * $uri = new Uri('http://www.kephp.com/test');
	 * $uri->setData('hello');  // http://www.kephp.com/test/hello/
	 * $uri->setData('../abc'); // http://www.kephp.com/test/hello/../abc/
	 * $uri->setData('/login'); // http://www.kephp.com/login/ 特别注意这个
	 * ```
	 *
	 * 合并查询字符
	 * ```php
	 * $uri->setData('hello_world?id=1');         // hello_world?id=1
	 * $uri->setData('hello_world?id=1', 'id=2'); // hello_world?id=2
	 * // 删除掉指定的字段
	 * $uri->setData('hello_world?id=1', ['id' => null, 'name' => 'kephp']); // hello_world?name=kephp  id已经被删除
	 * ```
	 *
	 * 如果$data是一个Uri的实例，会将这个实例克隆到自身上。
	 *
	 * @param array|object|string      $data
	 * @param null|array|object|string $mergeQuery 需要合并的查询字符
	 *
	 * @return $this|Uri
	 */
	public function setData($data, $mergeQuery = null)
	{
		if ($data instanceof static) {
			return $data->cloneTo($this);
		}
		$type = gettype($data);
		if ($type === KE_STR) {
			$type = KE_ARY;
			$data = parse_url($data);
		} elseif ($type === KE_OBJ) {
			$type = KE_ARY;
			$data = get_object_vars($data);
		}
		if (empty($data) || $type !== KE_ARY)
			return $this;
		if (isset($data['uri'])) {
			$uri = $data['uri'];
			unset($data['uri']);
			return $this->setData($data, $mergeQuery)->setData($uri, $mergeQuery);
		}

		foreach (['scheme', 'host', 'port', 'path', 'fragment'] as $name) {
			if (isset($data[$name])) {
				call_user_func([$this, 'set' . $name], $data[$name]);
				unset($data[$name]);
			}
		}

		if (!empty($data['query'])) {
			$this->setQuery($data['query'], $mergeQuery);
			unset($data['query']);
		} elseif (!empty($mergeQuery)) {
			$this->setQuery($mergeQuery);
		}

		if (isset($data['user'])) {
			$this->setUserInfo($data['user'], $data['pass'] ?? null);
			unset($data['user'], $data['pass']);
		}

		if (!empty($data))
			$this->filterData($data);

		return $this;
	}

	/**
	 * 过滤数据
	 *
	 * 该函数实际上是提供给后继的继承的类来处理自定义的数据使用的。非uri的标准字段的数据都会被交给这个部分来处理。
	 *
	 * @param array $data
	 *
	 * @return $this
	 */
	protected function filterData(array $data)
	{
		$this->data = array_merge($this->data, $data);
		return $this;
	}

	/**
	 * 数组形式取回当前Uri实例的数据
	 *
	 * @return array
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * 动态获取属性的魔术方法实现
	 *
	 * @param string $field
	 *
	 * @return array|mixed|null|string
	 */
	public function __get($field)
	{
		if ($field === 'port') {
			return $this->getPort();
		} elseif ($field === 'hostPort') {
			return $this->getHost(true);
		} elseif ($field === 'authority') {
			return $this->getAuthority();
		} elseif ($field === 'path') {
			return $this->getPath();
		} elseif ($field === 'uri') {
			return $this->toUri();
		} elseif ($field === 'fullUri') {
			return $this->toUri(true);
		} elseif ($field === 'userInfo') {
			return $this->getUserInfo();
		} elseif ($field === 'query') {
			return $this->queryData;
		} elseif ($field === 'queryString') {
			return empty($this->data['query']) ? '' : $this->data['query'];
		} else {
			return empty($this->data[$field]) ? '' : $this->data[$field];
		}
	}

	/**
	 * 动态设置属性的魔术方法
	 *
	 * @param string $field
	 * @param mixed  $value
	 */
	public function __set($field, $value)
	{
		if ($field === 'mergeQuery') {
			$this->mergeQuery($value);
		} elseif ($field === 'query') {
			$this->setQuery($value, false);
		} else {
			$this->setData([$field => $value]);
		}
	}

	/**
	 * 将自己的数据克隆并写入到指定的Uri实例对象上。
	 *
	 * @param Uri $clone
	 *
	 * @return $this
	 */
	public function cloneTo(Uri $clone)
	{
		$clone->isChangePath = true;
		$clone->data = array_intersect_key($this->data, $clone->data);
		$clone->queryData = $this->queryData;
		return $clone;
	}

	/**
	 * 基于当前的Uri克隆出一个新的Uri实例，如果指定了参数，会将参数写入到新的Uri实例
	 *
	 * @param null $uri
	 * @param null $mergeQuery
	 *
	 * @return $this
	 */
	public function newUri($uri = null, $mergeQuery = null)
	{
		$clone = $this->cloneTo(new static());
		if (isset($uri)) {
			$clone->setData($uri, $mergeQuery);
		}
		return $clone;
	}

	/**
	 * 设置uri的协议
	 *
	 * ```php
	 * $uri->setScheme('http://www.163.com/');
	 * $uri->setScheme('https');
	 * $uri->setScheme('ftp:'); // 写入的实际上是ftp
	 * ```
	 *
	 * 该方法会将scheme强制转为小写。
	 *
	 * @param string $scheme uri的协议
	 *
	 * @return $this
	 */
	public function setScheme($scheme)
	{
		if ($scheme !== $this->data['scheme']) {
			if (isset(self::$stdPorts[$scheme]))
				$this->data['scheme'] = $scheme;
			else {
				if (($scheme = strstr($scheme, ':', true)) !== false)
					$this->data['scheme'] = $scheme;
				$this->data['scheme'] = strtolower($this->data['scheme']);
			}
		}
		return $this;
	}

	/**
	 * 取得Uri的协议，如果Uri没指定协议，返回空字符
	 *
	 * @return string
	 */
	public function getScheme()
	{
		return empty($this->data['scheme']) ? '' : $this->data['scheme'];
	}

	/**
	 * 设置Uri的Host
	 *
	 * 该函数也会强制将host转为小写。
	 *
	 * 如果传入的$host包含scheme和port，该函数也会同时更新
	 *
	 * ```php
	 * $uri->setHost('192.168.1.100');
	 * $uri->setHost('http://www.kephp.com:90/'); // 分别写入了scheme: http, host: www.kephp.com, port: 90
	 * ```
	 *
	 * @param string $host
	 * @param null   $port
	 *
	 * @return $this
	 */
	public function setHost($host, $port = null)
	{
		if ($host !== $this->data['host']) {
			// 过滤 //www.163.com/
			$host = strtolower(trim($host, '/'));
			$scheme = null;
			if (($index = strpos($host, '//')) !== false) {
				// http://localhost => http, localhost
				// //localhost => localhost
				if ($index > 1)
					$scheme = substr($host, 0, $index - 1);
				$host = substr($host, $index + 2);
			}
			if (($index = strpos($host, ':')) !== false) {
				// localhost:90 => localhost, 90
				$port = substr($host, $index + 1);
				$host = substr($host, 0, $index);
			}
			$this->data['host'] = $host;
			if (!empty($scheme))
				$this->setScheme($scheme);
			if (isset($port))
				$this->setPort($port);
		}
		return $this;
	}

	/**
	 * 取得当前Uri的Host
	 *
	 * @param bool $withPort 是否返回包含端口号
	 *
	 * @return string
	 */
	public function getHost($withPort = false)
	{
		$host = empty($this->data['host']) ? '' : $this->data['host'];
		if ($withPort) {
			if (isset($this->data['port']))
				$host .= ':' . $this->data['port'];
		}
		return $host;
	}

	/**
	 * 设置Uri的Port
	 *
	 *
	 *
	 * @param string|int $port 端口号，允许是字符串的数值，如'99'
	 *
	 * @return $this
	 */
	public function setPort($port)
	{
		if (is_numeric($port))
			$this->data['port'] = (int)$port;
		return $this;
	}

	/**
	 * 取回Uri的Port
	 *
	 * @return int|null
	 */
	public function getPort()
	{
		return isset($this->data['port']) ? $this->data['port'] : null;
	}

	/**
	 * 设置uri的user和pass部分
	 *
	 * @param string      $user
	 * @param string|null $pass
	 *
	 * @return $this
	 */
	public function setUserInfo($user, $pass = null)
	{
		if ($user !== $this->data['user'])
			$this->data['user'] = $user;
		if (isset($pass) && $pass !== $this->data['pass'])
			$this->data['pass'] = $pass;
		return $this;
	}

	/**
	 * 取回uri的user和pass部分
	 *
	 * @return string
	 */
	public function getUserInfo()
	{
		if (empty($this->data['user']))
			return '';
		return $this->data['user'] . (empty($this->data['pass']) ? '' : ':' . $this->data['pass']);
	}

	/**
	 * 取回uri的Authority
	 *
	 * `[user[:password]@]host[:port]`
	 *
	 * @return string
	 */
	public function getAuthority()
	{
		if (empty($this->data['host']))
			return '';
		$result = $this->getUserInfo();
		if (!empty($result))
			$result .= '@';
		$result .= $this->getHost(true);
		return $result;
	}

	/**
	 * 设置Uri的路径
	 *
	 * $path允许包含`#`和`?`部分，并会自动将他们拆开
	 *
	 * ```php
	 * $uri->setPath('hello_world?id=1#part_1');
	 * ```
	 *
	 * 路径的第一个字符为`/`，表示为重置Uri的路径，否则则是在当前的路径基础上添加路径
	 *
	 * ```php
	 * $uri->setPath('plus');   // 叠加路径
	 * $uri->setPath('/reset'); // 重置路径
	 * ```
	 *
	 * 第二个参数`$isMergeQuery`，用来说明如果路径包含了QueryString，则此次的QueryString是替换还是合并。
	 *
	 * 同时，`$isMergeQuery`也可以是一个数组或字符串，作为附加的QueryString写入
	 *
	 * @param string $path         路径名
	 * @param mixed  $isMergeQuery 整型或布尔类型，表示是否合并查询字符，如果是字符、数组、对象，则表示合并查询字符，且追加合并。
	 *
	 * @return $this
	 */
	public function setPath($path, $isMergeQuery = false)
	{
		// 先对路径进行过滤
		$query = $fragment = null;
		if (($index = strpos($path, '#')) !== false) {
			$fragment = substr($path, $index + 1);
			$path = substr($path, 0, $index);
		}
		if (($index = strpos($path, '?')) !== false) {
			$query = substr($path, $index + 1);
			$path = substr($path, 0, $index);
		}
//		$path       = static::filterPath($path, $this->ignoreEndSlash);
		// 判断路径是否为绝对路径
		$isAbsolute = isset($path[0]) && $path[0] === '/';
		if (!$isAbsolute) {
			if (strlen($path) > 0) {
				$len = strlen($this->data['path']);
				if ($len === 0 || ($len > 0 && $this->data['path'][$len - 1] !== '/'))
					$this->data['path'] .= '/';
				$this->data['path'] .= $path;
				$this->isChangePath = true; // 修改了路径
				$this->filterPath = '';
			}
		} else {
			if (!empty($path) && $path !== $this->data['path']) {
				$this->data['path'] = $path;
				$this->isChangePath = true; // 修改了路径
				$this->filterPath = '';
			}
		}
		if (!empty($query))
			$this->setQuery($query, $isMergeQuery);
		if (!empty($fragment))
			$this->setFragment($fragment);
		return $this;
	}

	/**
	 * 设置绝对路径，处理方式与`setPath`一致，但是会确保一定是重置路径。
	 *
	 * @param string $path
	 *
	 * @return $this
	 */
	public function setAbsPath($path)
	{
		if (!isset($path[0]) || $path[0] !== '/')
			$path = '/' . $path;
		return $this->setPath($path);
	}

	/**
	 * 取回当前Uri的路径。现在改为在拿路径值的时候，filterPath
	 *
	 * @return string
	 */
	public function getPath()
	{
		if (empty($this->data['path'])) return '';
		if ($this->isChangePath) {
			$this->filterPath = static::filterPath($this->data['path'], $this->ignoreEndSlash);
			$this->isChangePath = false;
		}
		return $this->filterPath;
	}


	/**
	 * 设置Uri的查询字符串
	 *
	 * 第二个参数与`setPath`的第二个参数的作用相同。
	 *
	 * 如果不指定第二个参数，则默认是以当前写入的QueryString替换掉当前的QueryString
	 *
	 * ```php
	 * $uri = new Uri('/?id=1&name=a');                 // ?id=1&name=a
	 * $uri->setQuery('search=xxx');                    // ?search=xxx
	 * $uri->setQuery('search=xxx', true);              // ?id=1&name=a&search=xxx
	 * $uri->setQuery('search=xxx', 'id=2');            // ?id=2&name=a&search=xxx
	 * $uri->setQuery('search=xxx', ['id' => null]);    // ?name=a&search=xxx
	 * ```
	 *
	 * @param string|array|object $query      写入的查询字符，允许多种格式
	 * @param mixed               $mergeQuery 整型或布尔类型，表示是否合并查询字符，如果是字符、数组、对象，则表示合并查询字符，且追加合并。
	 *
	 * @return $this
	 */
	public function setQuery($query, $mergeQuery = null)
	{
		$isMerge = true; // 默认改为 true
		$mergeData = [];
		if ($mergeQuery === true || $mergeQuery === false) {
			$isMerge = $mergeQuery;
		} elseif (!empty($mergeQuery)) {
			$mergeData = $mergeQuery;
		}

		if (empty($query)) {
			$query = [];
		} else {
			$type = gettype($query);
			if ($type === KE_OBJ) {
				$query = get_object_vars($query);
			} elseif ($type === KE_ARY) {
				// @todo 严格来说，当query为一个数组的时候，应该循环遍历，并执行key, value的urlencode
			} else {
				// 强制转为字符串类型
				if ($type !== KE_STR)
					$query = (string)$query;
				if ($query[0] === '?')
					$query = ltrim($query, '?');
				parse_str($query, $query);
			}
		}
		// 合并query，先合并
		$isChangeQuery = false;

		if ($this->queryData !== $query) {
			$isChangeQuery = true;
			if ($isMerge)
				$this->queryData = array_merge($this->queryData, $query);
			else
				$this->queryData = $query;
		}

		// @todo 这里以后要改为，取值得时候，才 http build
		if ($isChangeQuery) {
			if (empty($this->queryData))
				$this->data['query'] = '';
			else
				$this->data['query'] = http_build_query($this->queryData, $this->queryNumericPrefix, $this->querySeparator, $this->queryEncodeType);
		}
		// 如果不为空，就是表示合并
		if (!empty($mergeData))
			$this->setQuery($mergeData, true);
		return $this;
	}

	/**
	 * 合并写入查询字符
	 *
	 * @param string|array|object $query
	 *
	 * @return $this
	 */
	public function mergeQuery($query)
	{
		return $this->setQuery($query, true);
	}

	/**
	 * 强制清理 Query 数据，这个需求非常需要！
	 *
	 * @return $this
	 */
	public function clearQuery()
	{
		$this->data['query'] = '';
		$this->queryData = [];
		return $this;
	}

	/**
	 * 获得当前Uri的查询字符，注意这个方法返回的是字符格式
	 *
	 * @return string
	 */
	public function getQuery()
	{
		return empty($this->data['query']) ? '' : $this->data['query'];
	}

	/**
	 * 以数组形式取回当前Uri的查询字符
	 *
	 * @return array
	 */
	public function getQueryData()
	{
		return $this->queryData;
	}

	/**
	 * 查询并取回当前Uri的QueryString某个key的值
	 *
	 * 该函数允许深度查询
	 *
	 * ```php
	 * $uri->query('id'); // 有则返回id，没有则返回null
	 * $uri->query('id', false); // 指定当id不存在时的默认返回值
	 * $uri->query('words->a');  // 深度查询
	 * ```
	 *
	 * @param string|array $keys
	 * @param null         $default
	 *
	 * @return mixed
	 */
	public function query($keys, $default = null)
	{
		if (isset($this->queryData[$keys]))
			return $this->queryData[$keys];
		return depth_query($this->queryData, $keys, $default);
	}

	/**
	 * 设置Uri的fragment
	 *
	 * @param string $fragment
	 *
	 * @return $this
	 */
	public function setFragment($fragment)
	{
		if (isset($fragment[0]) && $fragment[0] === '#')
			$fragment = ltrim($fragment, '#');
		$this->data['fragment'] = $fragment;
		return $this;
	}

	/**
	 * 取回Fragment
	 *
	 * @return string
	 */
	public function getFragment()
	{
		return empty($this->data['fragment']) ? '' : $this->data['fragment'];
	}

	/**
	 * 获得当前Uri的字符输出内容
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->toUri();
	}

	/**
	 * 生成字符串Uri
	 *
	 * 允许指定参数是否强制包含Authority信息。
	 *
	 * 如当前PHP的全局信息是：http://www.kephp.com/test/
	 *
	 * 而Uri的数据为：http://www.kephp.com/test/admin/post/edit/1 （这个Uri为本地Uri）
	 *
	 * 在不指定`$isWithAuthority`时，`toUri`方法会返回`/test/admin/post/edit/1`，而不包含Authority信息。
	 *
	 * @param null $isWithAuthority
	 *
	 * @return string
	 */
	public function toUri($isWithAuthority = null)
	{
		if (!isset($isWithAuthority))
			$isWithAuthority = !$this->isHideAuthority();
		$uri = '';
		if ($isWithAuthority) {
			$uri = $this->getScheme();
			if (!empty($uri))
				$uri .= ':';
			$authority = $this->getAuthority();
			if (!empty($authority)) {
				$uri .= '//' . $authority;
			}
		}
		$path = $this->getPath();
		if (!empty($this->data['host']) && (empty($path) || isset($path[0]) && $path[0] !== '/'))
			$uri .= '/';
		$uri .= $path;
		if (!empty($this->data['query']))
			$uri .= '?' . $this->data['query'];
		if (!empty($this->data['fragment']))
			$uri .= '#' . $this->data['fragment'];
		return $uri;
	}

	/**
	 * 判断当前Uri是否为本地Uri
	 *
	 * 这里所谓的本地，不是指host是否为localhost，而是指这个Uri的scheme和host是否和全局信息（$_SERVER）相同，相同则表示为本地Uri。
	 *
	 * @return bool
	 */
	public function isLocalhost()
	{
		if (isset($this->data['scheme']) && $this->data['scheme'] === KE_REQUEST_SCHEME &&
			$this->getHost(true) === KE_REQUEST_HOST
		) {
			return true;
		}
		return false;
	}

	/**
	 * 是否隐藏Authority
	 *
	 * @return bool
	 */
	public function isHideAuthority()
	{
		if ($this->isHideAuthority)
			return true;
		if (empty($this->data['host']))
			return true;
		if ($this->isLocalhost())
			return true;
		return false;
	}

	/**
	 * 设置uri是否隐藏Authority
	 *
	 * @param bool $isHide
	 *
	 * @return $this
	 */
	public function setHideAuthority($isHide)
	{
		$this->isHideAuthority = (bool)$isHide;
		return $this;
	}

	public function setCleanEndSlash($isClean)
	{
		// 兼容旧版本接口
		// true => 表示忽略末尾路径，输入有就有，没有就没有，不管
		// false => 表示不忽略末尾末路的出路，则强制检测是否为文件结尾，不是文件结尾，强制补回一个 /
		return $this->setIgnoreEndSlash(!!$isClean);
	}

	public function setIgnoreEndSlash($ignore)
	{
		$this->ignoreEndSlash = !!$ignore;
		return $this;
	}
}