<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Web;

use Ke\Uri;

/**
 * Http类，类似URL，作为静态类时，是一些Http常用的辅助方法，作为一个Http实例对象时，是一次Http请求所携带的数据。
 *
 * 一次Http请求的头部，会类似如下的数据
 *
 * ```http
 * GET /hello/world.html?id=123 HTTP/1.1
 * Host: 192.168.1.100:8080
 * User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.0
 * Accept: text/html,application/xhtml+xml,application/xml...
 * Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3
 * Accept-Encoding: gzip, deflate
 * Connection: keep-alive
 * Cache-Control: max-age=0
 * ```
 *
 * @package Ke\Web
 * @property string $method
 * @property string $protocol
 * @property string $ua
 * @property array $media
 * @property array $language
 * @property array $encoding
 * @property string $referer
 * @property string $connection
 * @property string $cacheControl
 * @property string $xRequested
 * @property boolean $isXhr
 * @property boolean $isDnt
 * @property boolean $isHttps
 * @property boolean $isHttp2
 */
class Http extends Uri
{

	use HttpSecurityData;

	const GET = 'GET';
	const POST = 'POST';
	const DELETE = 'DELETE';
	const PUT = 'PUT';

	const X_REQUEST_WITH = 'HTTP_X_REQUESTED_WITH';

	const XHR_VALUE = 'xmlhttprequest';

	const FLASH_USER_AGENT = 'Shockwave Flash';

	const HTTP_10 = 'HTTP/1.0';
	const HTTP_11 = 'HTTP/1.1';
	const HTTP_20 = 'HTTP/2.0';

	/** @var static */
	private static $currentHttp = null;

	private static $stdMethods = [
		self::GET    => 1,
		self::POST   => 1,
		self::DELETE => 1,
		self::PUT    => 1,
	];

	protected static $serverKeys = [
		'REQUEST_METHOD'       => 'method',
		'SERVER_PROTOCOL'      => 'protocol', // if HTTP/2.0 let it show
		'HTTP_USER_AGENT'      => 'ua',
		'HTTP_ACCEPT'          => 'medias',
		'HTTP_ACCEPT_LANGUAGE' => 'languages',
		'HTTP_ACCEPT_ENCODING' => 'encodings',
		'HTTP_REFERER'         => 'referer',
		'HTTP_CONNECTION'      => 'connection',
		'HTTP_CACHE_CONTROL'   => 'cacheControl',
		'HTTP_DNT'             => 'isDnt',
		self::X_REQUEST_WITH   => 'xRequested',
	];

	protected $data = [
		'scheme'       => null,
		'host'         => null,
		'user'         => null,
		'pass'         => null,
		'port'         => null,
		'path'         => null,
		'query'        => null,
		'fragment'     => null,
		'method'       => self::GET,
		'protocol'     => null,
		'ua'           => null,
		'medias'       => [],
		'languages'    => [],
		'encodings'    => [],
		'referer'      => null,
		//		'contentType'   => null,
		//		'contentLength' => 0,
		'connection'   => null,
		'cacheControl' => null,
		'xRequested'   => null,
		'isDnt'        => false,
		'isXhr'        => false,
		'isHttps'      => false,
		'isHttp2'      => false,
	];

	protected $features = [];

	private $isParseUA = false;

	public $post = null;

	public $files = null;

	public $cookies = null;

	public static function parseAccept($str): array
	{
		$languages = [];
		if (!empty($str)) {
			// zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3
			foreach (explode(';', $str) as $index => $segment) {
				$split = explode(',', $segment);
				// 首位，表示的是默认的当前语言
				if ($index === 0) {
					foreach ($split as $lang) {
						$languages[trim($lang)] = 1; // 一定要清理空格
					}
				} else if (isset($split[1])) { // q=0.5,en有效，q=0.3，就无效了
					$pos = strpos($split[0], '=');
					$quality = 0.1;
					if ($pos !== false)
						$quality = (float)substr($split[0], $pos + 1);
					if ($quality > 0)
						$languages[trim($split[1])] = $quality;
				}
			}
		}
		return $languages;
	}

	public static function getCurrentData(): array
	{
		$data = [
			'scheme' => KE_REQUEST_SCHEME,
			'host'   => KE_REQUEST_HOST,
			'uri'    => KE_REQUEST_URI,
		];
		foreach ($_SERVER as $key => $value) {
			if (isset(static::$serverKeys[$key])) {
				$data[static::$serverKeys[$key]] = $value;
			} else if (strpos($key, 'HTTP_') === 0) {
				$field = strtolower(substr($key, 5));
				if (!isset($data[$field]))
					$data[$field] = $value;
			}
			continue;
		}
		$data['xRequested'] = empty($data['xRequested']) ? '' : $data['xRequested'];
		$data['isXhr'] = mb_strtolower($data['xRequested']) === self::XHR_VALUE; // 严格识别 XHR 请求，很多冒充
		$data['isHttps'] = KE_REQUEST_SCHEME === 'https';
		$data['isHttp2'] = isset($data['protocol']) ? $data['protocol'] === self::HTTP_20 : false;
		return $data;
	}

	/**
	 * @return Http
	 */
	public static function current()
	{
		if (!isset(self::$currentHttp)) {
			self::$currentHttp = new static(static::getCurrentData());
			self::$currentHttp->post = &$_POST;
			self::$currentHttp->files = &$_FILES;
			self::$currentHttp->cookies = &$_COOKIE;
		}
		return self::$currentHttp;
	}

	public function __construct($data = null)
	{
		parent::__construct($data);
	}

	protected function filterData(array $data)
	{
		$methods = [
			'method'    => 'setMethod',
			'ua'        => 'setUserAgent',
			'protocol'  => 'setProtocol',
			'medias'    => 'setMedias',
			'languages' => 'setLanguages',
			'encodings' => 'setEncodings',
			'referer'   => 'setReferer',
		];
		foreach ($methods as $field => $method) {
			if (isset($data[$field])) {
				call_user_func([$this, $method], $data[$field]);
				unset($data[$field]);
			}
		}
		if (!empty($data))
			$this->data = array_merge($this->data, $data);
		return $this;
	}

	public function setMethod(string $method)
	{
		if (empty($method)) {
			$this->data['method'] = self::GET;
		} else if ($method !== $this->data['method']) {
			if (!isset(self::$stdMethods[$method]))
				$method = strtoupper($method);
			if (!isset(self::$stdMethods[$method]))
				$method = self::GET;
			$this->data['method'] = $method;
		}
		return $this;
	}

	public function getMethod(): string
	{
		return $this->data['method'];
	}

	public function setUserAgent(string $ua)
	{
		$this->data['ua'] = $ua;
		return $this;
	}

	public function getUserAgent(): string
	{
		return empty($this->data['ua']) ? '' : $this->data['ua'];
	}

	public function setProtocol(string $protocol)
	{
		if (empty($protocol) || ($protocol !== self::HTTP_10 && $protocol !== self::HTTP_11))
			$protocol = self::HTTP_11;
		if ($protocol !== $this->data['protocol'])
			$this->data['protocol'] = $protocol;
		return $this;
	}

	public function getProtocol(): string
	{
		return $this->data['protocol'];
	}

	public function setMedias($data)
	{
		if (!empty($data)) {
			$type = gettype($data);
			if ($type === KE_STR) {
				$type = KE_ARY;
				$data = static::parseAccept($data);
			}
			if ($type === KE_ARY && !empty($data))
				$this->data['medias'] = $data;
		}
		return $this;
	}

	public function getMedias(): array
	{
		return $this->data['medias'];
	}

	public function setLanguages($data)
	{
		if (!empty($data)) {
			$type = gettype($data);
			if ($type === KE_STR) {
				$type = KE_ARY;
				$data = static::parseAccept($data);
			}
			if ($type === KE_ARY && !empty($data))
				$this->data['languages'] = $data;
		}
		return $this;
	}

	public function getLanguages(): array
	{
		return $this->data['languages'];
	}

	public function setEncodings($data)
	{
		if (!empty($data)) {
			$type = gettype($data);
			if ($type === KE_STR) {
				$type = KE_ARY;
				$data = static::parseAccept($data);
			}
			if ($type === KE_ARY && !empty($data))
				$this->data['encodings'] = $data;
		}
		return $this;
	}

	public function getEncodings(): array
	{
		return $this->data['encodings'];
	}

	public function setReferer(string $referer)
	{
		if ($referer !== $this->data['referer'])
			$this->data['referer'] = $referer;
		return $this;
	}

	public function getReferer(): string
	{
		return empty($this->data['referer']) ? '' : $this->data['referer'];
	}

	public function post($keys = null, $default = null)
	{
		if (empty($keys))
			return $this->post;
//		if (isset($this->post[$keys]))
//			return $this->post[$keys];
		return depth_query($this->post, $keys, $default);
	}

	public function cookie($keys = null, $default = null)
	{
		if (empty($keys))
			return $this->cookies;
//		if (isset($this->cookies[$keys]))
//			return $this->cookies[$keys];
		return depth_query($this->cookies, $keys, $default);
	}

	public function getSecurityWatchData()
	{
		return $this->post;
	}

	public function isPost($prefix = null, $expire = 0)
	{
		$isPost = $this->data['method'] === self::POST;
		if (isset($prefix)) {
			if (!$this->isSecurityPrepared())
				$this->prepareSecurity();
			$isPost = $prefix === $this->getSecurityPrefix();
			if ($isPost) {
				$expire = !is_numeric($expire) || $expire < 0 ? 0 : round(floatval($expire), 4);
				if ($expire > 0) {
					$diff = round(microtime(true) - $this->getSecurityTimestamp());
					return $diff <= $expire;
				}
			}
		}
		return $isPost;
	}

	/**
	 * 取得是否具有某个特征
	 *
	 * @param string $feature
	 * @return bool
	 */
	public function is(string $feature): bool
	{
		if (!$this->isParseUA) {
			$this->features = $this->parseUA($this->data['ua']);
		}
		return !empty($this->features[mb_strtolower($feature)]);
	}

	public function getFeature(string $feature)
	{
		if (!$this->isParseUA) {
			$this->features = $this->parseUA($this->data['ua']);
		}
		return $this->features[mb_strtolower($feature)] ?? false;
	}

	// public function setUAParser()
	// {
	//
	// }

	public function parseUA(string $string = null): array
	{
		$features = [];
		if (!empty($string)) {
			if (stripos($string, 'android') !== false)
				$features['android'] = true;
			else if (stripos($string, 'iPhone') !== false) {
				$features['iphone'] = true;
				$features['ios'] = true;
			} else if (stripos($string, 'iPad') !== false) {
				$features['ipad'] = true;
				$features['ios'] = true;
			}
			if (preg_match('#(?:MicroMessenger\/([^\s]+))#i', $string, $matches)) {
				$features['wx'] = $matches[1];
			}
		}
		return $features;
	}
}
