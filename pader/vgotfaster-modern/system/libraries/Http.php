<?php
/**
 * VgotFaster PHP Framework
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2014, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

namespace VF\Library;

/**
 * VgotFaster Http Client
 *
 * @package VgotFaster
 * @subpackage Library
 * @author pader
 */
class Http {

	protected $requestHeaders = array();
	protected $requestCookie = array();
	protected $hostIp = '';
	protected $connectTimeout = 15;
	protected $timeout = 30;
	protected $requestDetail = array();

	/**
	 * boundary of multipart
	 * @ignore
	 */
	protected static $boundary = '';

	public function __construct($set=null) {
		if (!function_exists('fsockopen')) {
			showError('Can not use library HTTP, because function <b>fsockopen</b> doesn\'t exists!');
		}

		$VF =& getInstance();

		if($VF->config->test('http')) {
			$config = $VF->config->get('http');
			$this->initialize($config);
		}

		if ($set) {
			$this->initialize($set);
		}
	}

	/**
	 * 初始化设置
	 *
	 * @param array $config (cookies/headers/host_ip/user_agent/timeout/connect_timeout/request_timeout)
	 * @return void
	 */
	public function initialize($config) {
		//Header
		if (isset($config['headers'])) {
			foreach ($config['headers'] as $name => $val) {
				$this->setHeader($name, $val);
			}
		}

		//Cookie
		if (isset($config['cookies'])) {
			foreach ($config['cookies'] as $name => $val) {
				$this->setCookie($name, $val);
			}
		}

		$singleParams = array(
			'host_ip' => 'setHostIp',
			'user_agent' => 'setUserAgent',
			'timeout' => 'setTimeout',
			'connect_timeout' => 'setConnectTimeout',
			'request_timeout' => 'setRequestTimeout',
			'follow_redirect' => 'setFollowRecirect'
		);

		foreach ($singleParams as $name => $method) {
			if (isset($config[$name])) {
				$this->$method($config[$name]);
			}
		}
	}

	/**
	 * 设置请求 Cookie
	 *
	 * @param string $name
	 * @param string $val
	 * @return void
	 */
	public function setCookie($name, $val) {
		$this->requestCookie[$name] = $val;
	}

	/**
	 * 设置请求 Header
	 *
	 * @param string $name
	 * @param string $val
	 * @return void
	 */
	public function setHeader($name, $val) {
		$this->requestHeaders[$name] = $val;
	}

	/**
	 * 设置请求用户 User-Agent
	 *
	 * @param string $userAgent
	 * @return void
	 */
	public function setUserAgent($userAgent) {
		$this->setHeader('User-Agent', $userAgent);
	}

	/**
	 * 设置请求的主机 IP 地址（相当于 DNS 指定 IP）
	 *
	 * @param string $ip
	 * @return void
	 */
	public function setHostIp($ip) {
		$this->hostIp = $ip;
	}

	/**
	 * 设置连接超时时间
	 *
	 * @param int $seconds
	 * @return void
	 */
	public function setConnectTimeout($seconds) {
		$this->connectTimeout = $seconds;
	}

	/**
	 * 设置获取数据响应超时间
	 *
	 * @param mixed $seconds
	 * @return void
	 */
	public function setRequestTimeout($seconds) {
		$this->timeout = $seconds;
	}

	/**
	 * 设置超时时间
	 *
	 * 此超时时间会同时覆盖连接与数据响应超时时间
	 *
	 * @param int $seconds
	 * @return void
	 */
	public function setTimeout($seconds) {
		$this->connectTimeout = $seconds;
		$this->timeout = $seconds;
	}

	/**
	 * 清除所有设置，包括上次请求的信息
	 *
	 * @return void
	 */
	public function clear() {
		$this->requestHeaders = array();
		$this->requestCookie = array();
		$this->hostIp = '';
		$this->connectTimeout = 15;
		$this->timeout = 30;
		$this->requestDetail = array();
	}

	/**
	 * GET
	 *
	 * @param string $url
	 * @param array $params
	 * @return string
	 */
	public function get($url, $params=array()) {
		$params && $url .= (strpos($url, '?') ? '&' : '?').http_build_query($params);
		return $this->sendRequest($url, 'GET', null);
	}

	/**
	 * POST
	 *
	 * @param string $url
	 * @param array $params
	 * @param bool $multi
	 * @return string
	 */
	public function post($url, $params, $multi=false) {
		if (!$multi && (is_array($params) || is_object($params)) ) {
			$body = http_build_query($params);
			$this->setHeader('Content-Type', 'application/x-www-form-urlencoded');
		} else {
			$body = self::build_http_query_multi($params);
			$this->setHeader('Content-Type', 'multipart/form-data; boundary='.self::$boundary);
		}

		return $this->sendRequest($url, 'POST', $body);
	}

	/**
	 * DELETE
	 *
	 * @param string $url
	 * @param array $params
	 * @return string
	 */
	public function delete($url, $params) {
		$params && $url .= (strpos($url, '?') ? '&' : '?').http_build_query($params);
		return $this->sendRequest($url, 'DELETE');
	}

	/**
	 * PUT
	 *
	 * @param string $url
	 * @param array $params
	 * @return
	 */
	public function put($url, $params) {
		$body = http_build_query($params);
		$this->setHeader('Content-Type', 'application/x-www-form-urlencoded');

		return $this->sendRequest($url, 'PUT', $body);
	}

	/**
	 * 获取上次请求的细节信息
	 *
	 * @param string $field
	 * @return mixed
	 */
	public function getRequestDetail($field=null) {
		return $this->requestDetail;
	}

	/**
	 * HTTP 请求
	 *
	 * @param string $url
	 * @param string $method
	 * @param string $postbody
	 * @return string
	 */
	public function sendRequest($url, $method, $postbody=null) {
		$timeout = 15;

		$parse = parse_url($url);

		isset($parse['host']) ||$parse['host'] = '';
		isset($parse['path']) || $parse['path'] = '';
		isset($parse['query']) || $parse['query'] = '';
		isset($parse['port']) || $parse['port'] = '';

		$path = $parse['path'] ? $parse['path'].($parse['query'] ? '?'.$parse['query'] : '') : '/';
		$host = $this->hostIp ? $this->hostIp : $parse['host'];

		$this->requestDetail['method'] = $method;
		$this->requestDetail['url'] = $url;
		$this->requestDetail['host'] = $host;

		//协议
		if ($parse['scheme'] == 'https') {
			$version = '1.1';
			$port = empty($parse['port']) ? 443 : $parse['port'];
			$host = 'ssl://'.$host;
		} else {
			$version = '1.0';
			$port = empty($parse['port']) ? 80 : $parse['port'];
		}

		$this->requestDetail['port'] = $port;

		//Headers
		$this->requestHeaders['Connection'] = 'Close';
		$this->requestHeaders['Accept'] = '*/*';

		//Cookie
		if ($this->requestCookie) {
			$cookie = array_map('urlencode', $this->requestCookie);
			$cookie = join('; ', $cookie);
			$this->requestHeaders['Cookie'] = $cookie;
		}

		//包体信息
		$headers = "Host: {$parse['host']}";

		foreach ($this->requestHeaders as $name => $val) {
			$headers != '' && $headers .= "\r\n";
			$headers .= $name.': '.$val;
		}

		$this->requestDetail['request_headers'] = $headers;
		
		$out = "$method $path HTTP/$version\r\n";

		if ($method == 'POST' || $method == 'PUT') {
			$headers .= "\r\nContent-Length: ".strlen($postbody);
			$out .= $headers."\r\n\r\n".$postbody;
		} else {
			$out .= $headers."\r\n\r\n";
		}

		//发送请求
		$limit = 0;
		$fp = fsockopen($host, $port, $errno, $errstr, $this->connectTimeout);

		if (!$fp) {
			showError('Failed to establish socket connection: '.$url, true, false, 'HTTP Request Error');
		} else {
			$header = $content = '';

			stream_set_blocking($fp, true);
			stream_set_timeout($fp, $this->timeout);
			fwrite($fp, $out);
			$status = stream_get_meta_data($fp);
			$content = '';

			if (!$status['timed_out']) { //未超时
				while (!feof($fp)) {
					$header .= $h = fgets($fp);
					if ($h && ($h == "\r\n" ||  $h == "\n")) break;
					if (strpos($h, 'Content-Length:') !== false) {
						$limit = intval(substr($header, 15));
					}
				}

				$this->requestDetail['response_headers'] = $header;

                if (preg_match("|^HTTP/[^\s]*\s(.*?)\s|", $header, $status)) {
					$this->requestDetail['status_code'] = $status[1];
                }

				$stop = false;
				while (!feof($fp) && !$stop) {
					$data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
					$content .= $data;
					if ($limit) {
						$limit -= strlen($data);
						$stop = $limit <= 0;
					}
				}
			}
			fclose($fp);

			//unchunk
			$content = preg_replace_callback(
		        '/(?:(?:\r\n|\n)|^)([0-9A-F]+)(?:\r\n|\n){1,2}(.*?)'.
		        '((?:\r\n|\n)(?:[0-9A-F]+(?:\r\n|\n))|$)/si',
		        create_function(
		            '$matches',
		            'return hexdec($matches[1]) == strlen($matches[2]) ? $matches[2] : $matches[0];'
		        ),
		        $content
		    );

			return $content;
		}
	}

	/**
	 * 建立文件上传包体信息
	 *
	 * @ignore
	 */
	protected static function build_http_query_multi($params) {
		if (!$params) return '';

		uksort($params, 'strcmp');

		$pairs = array();

		self::$boundary = $boundary = uniqid('------------------');
		$MPboundary = '--'.$boundary;
		$endMPboundary = $MPboundary. '--';
		$multipartbody = '';

		foreach ($params as $parameter => $value) {
			if ($parameter{0} == '@') {
				$parameter = substr($parameter, 1);
                $content = $value[2];
                $filename = $value[0];
                //$mime = $value[1];

				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
				$multipartbody .= "Content-Type: application/octet-stream\r\n\r\n";
				$multipartbody .= $content. "\r\n";
			} else {
				$multipartbody .= $MPboundary . "\r\n";
				$multipartbody .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
				$multipartbody .= $value."\r\n";
			}
		}

		$multipartbody .= $endMPboundary;
		return $multipartbody;
	}

}
