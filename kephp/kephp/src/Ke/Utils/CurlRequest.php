<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Utils;

use Ke\MimeType;
use Ke\Uri;
use voku\helper\AntiXSS;

class CurlRequest
{

	const CURLOPT_KEY_MAPS = [
		CURLOPT_AUTOREFERER             => 'autoreferer',
		CURLOPT_BINARYTRANSFER          => 'binarytransfer',
		CURLOPT_BUFFERSIZE              => 'buffersize',
		CURLOPT_CAINFO                  => 'cainfo',
		CURLOPT_CAPATH                  => 'capath',
		CURLOPT_CONNECTTIMEOUT          => 'connecttimeout',
		CURLOPT_COOKIE                  => 'cookie',
		CURLOPT_COOKIEFILE              => 'cookiefile',
		CURLOPT_COOKIEJAR               => 'cookiejar',
		CURLOPT_COOKIESESSION           => 'cookiesession',
		CURLOPT_CRLF                    => 'crlf',
		CURLOPT_CUSTOMREQUEST           => 'customrequest',
		CURLOPT_DNS_CACHE_TIMEOUT       => 'dns_cache_timeout',
		CURLOPT_DNS_USE_GLOBAL_CACHE    => 'dns_use_global_cache',
		CURLOPT_EGDSOCKET               => 'egdsocket',
		CURLOPT_ENCODING                => 'encoding',
		CURLOPT_FAILONERROR             => 'failonerror',
		CURLOPT_FILE                    => 'file',
		CURLOPT_FILETIME                => 'filetime',
		CURLOPT_FOLLOWLOCATION          => 'followlocation',
		CURLOPT_FORBID_REUSE            => 'forbid_reuse',
		CURLOPT_FRESH_CONNECT           => 'fresh_connect',
		CURLOPT_FTPAPPEND               => 'ftpappend',
		CURLOPT_FTPLISTONLY             => 'ftplistonly',
		CURLOPT_FTPPORT                 => 'ftpport',
		CURLOPT_FTP_USE_EPRT            => 'ftp_use_eprt',
		CURLOPT_FTP_USE_EPSV            => 'ftp_use_epsv',
		CURLOPT_HEADER                  => 'header',
		CURLOPT_HEADERFUNCTION          => 'headerfunction',
		CURLOPT_HTTP200ALIASES          => 'http200aliases',
		CURLOPT_HTTPGET                 => 'httpget',
		CURLOPT_HTTPHEADER              => 'httpheader',
		CURLOPT_HTTPPROXYTUNNEL         => 'httpproxytunnel',
		CURLOPT_HTTP_VERSION            => 'http_version',
		CURLOPT_INFILE                  => 'infile',
		CURLOPT_INFILESIZE              => 'infilesize',
		CURLOPT_INTERFACE               => 'interface',
		CURLOPT_KRB4LEVEL               => 'krb4level',
		CURLOPT_LOW_SPEED_LIMIT         => 'low_speed_limit',
		CURLOPT_LOW_SPEED_TIME          => 'low_speed_time',
		CURLOPT_MAXCONNECTS             => 'maxconnects',
		CURLOPT_MAXREDIRS               => 'maxredirs',
		CURLOPT_NETRC                   => 'netrc',
		CURLOPT_NOBODY                  => 'nobody',
		CURLOPT_NOPROGRESS              => 'noprogress',
		CURLOPT_NOSIGNAL                => 'nosignal',
		CURLOPT_PORT                    => 'port',
		CURLOPT_POST                    => 'post',
		CURLOPT_POSTFIELDS              => 'postfields',
		CURLOPT_POSTQUOTE               => 'postquote',
		CURLOPT_PREQUOTE                => 'prequote',
		CURLOPT_PRIVATE                 => 'private',
		CURLOPT_PROGRESSFUNCTION        => 'progressfunction',
		CURLOPT_PROXY                   => 'proxy',
		CURLOPT_PROXYPORT               => 'proxyport',
		CURLOPT_PROXYTYPE               => 'proxytype',
		CURLOPT_PROXYUSERPWD            => 'proxyuserpwd',
		CURLOPT_PUT                     => 'put',
		CURLOPT_QUOTE                   => 'quote',
		CURLOPT_RANDOM_FILE             => 'random_file',
		CURLOPT_RANGE                   => 'range',
		CURLOPT_READDATA                => 'readdata',
		CURLOPT_READFUNCTION            => 'readfunction',
		CURLOPT_REFERER                 => 'referer',
		CURLOPT_RESUME_FROM             => 'resume_from',
		CURLOPT_RETURNTRANSFER          => 'returntransfer',
		CURLOPT_SHARE                   => 'share',
		CURLOPT_SSLCERT                 => 'sslcert',
		CURLOPT_SSLCERTPASSWD           => 'sslcertpasswd',
		CURLOPT_SSLCERTTYPE             => 'sslcerttype',
		CURLOPT_SSLENGINE               => 'sslengine',
		CURLOPT_SSLENGINE_DEFAULT       => 'sslengine_default',
		CURLOPT_SSLKEY                  => 'sslkey',
		CURLOPT_SSLKEYPASSWD            => 'sslkeypasswd',
		CURLOPT_SSLKEYTYPE              => 'sslkeytype',
		CURLOPT_SSLVERSION              => 'sslversion',
		CURLOPT_SSL_CIPHER_LIST         => 'ssl_cipher_list',
		CURLOPT_SSL_VERIFYHOST          => 'ssl_verifyhost',
		CURLOPT_SSL_VERIFYPEER          => 'ssl_verifypeer',
		CURLOPT_STDERR                  => 'stderr',
		CURLOPT_TELNETOPTIONS           => 'telnetoptions',
		CURLOPT_TIMECONDITION           => 'timecondition',
		CURLOPT_TIMEOUT                 => 'timeout',
		CURLOPT_TIMEVALUE               => 'timevalue',
		CURLOPT_TRANSFERTEXT            => 'transfertext',
		CURLOPT_UNRESTRICTED_AUTH       => 'unrestricted_auth',
		CURLOPT_UPLOAD                  => 'upload',
		CURLOPT_URL                     => 'url',
		CURLOPT_USERAGENT               => 'useragent',
		CURLOPT_USERPWD                 => 'userpwd',
		CURLOPT_VERBOSE                 => 'verbose',
		CURLOPT_WRITEFUNCTION           => 'writefunction',
		CURLOPT_WRITEHEADER             => 'writeheader',
		CURLOPT_HTTPAUTH                => 'httpauth',
		CURLOPT_FTP_CREATE_MISSING_DIRS => 'ftp_create_missing_dirs',
		CURLOPT_PROXYAUTH               => 'proxyauth',
		CURLOPT_FTP_RESPONSE_TIMEOUT    => 'ftp_response_timeout',
		CURLOPT_IPRESOLVE               => 'ipresolve',
		CURLOPT_MAXFILESIZE             => 'maxfilesize',
		CURLOPT_FTP_SSL                 => 'ftp_ssl',
		CURLOPT_NETRC_FILE              => 'netrc_file',
		CURLOPT_FTPSSLAUTH              => 'ftpsslauth',
		CURLOPT_FTP_ACCOUNT             => 'ftp_account',
		CURLOPT_TCP_NODELAY             => 'tcp_nodelay',
		CURLOPT_COOKIELIST              => 'cookielist',
		CURLOPT_IGNORE_CONTENT_LENGTH   => 'ignore_content_length',
		CURLOPT_FTP_SKIP_PASV_IP        => 'ftp_skip_pasv_ip',
		CURLOPT_FTP_FILEMETHOD          => 'ftp_filemethod',
		CURLOPT_CONNECT_ONLY            => 'connect_only',
		CURLOPT_LOCALPORT               => 'localport',
		CURLOPT_LOCALPORTRANGE          => 'localportrange',
		CURLOPT_FTP_ALTERNATIVE_TO_USER => 'ftp_alternative_to_user',
		CURLOPT_MAX_RECV_SPEED_LARGE    => 'max_recv_speed_large',
		CURLOPT_MAX_SEND_SPEED_LARGE    => 'max_send_speed_large',
		CURLOPT_SSL_SESSIONID_CACHE     => 'ssl_sessionid_cache',
		CURLOPT_FTP_SSL_CCC             => 'ftp_ssl_ccc',
		CURLOPT_SSH_AUTH_TYPES          => 'ssh_auth_types',
		CURLOPT_SSH_PRIVATE_KEYFILE     => 'ssh_private_keyfile',
		CURLOPT_SSH_PUBLIC_KEYFILE      => 'ssh_public_keyfile',
		CURLOPT_CONNECTTIMEOUT_MS       => 'connecttimeout_ms',
		CURLOPT_HTTP_CONTENT_DECODING   => 'http_content_decoding',
		CURLOPT_HTTP_TRANSFER_DECODING  => 'http_transfer_decoding',
		CURLOPT_TIMEOUT_MS              => 'timeout_ms',
		CURLOPT_KRBLEVEL                => 'krblevel',
		CURLOPT_NEW_DIRECTORY_PERMS     => 'new_directory_perms',
		CURLOPT_NEW_FILE_PERMS          => 'new_file_perms',
		CURLOPT_APPEND                  => 'append',
		CURLOPT_DIRLISTONLY             => 'dirlistonly',
		CURLOPT_USE_SSL                 => 'use_ssl',
		CURLOPT_SSH_HOST_PUBLIC_KEY_MD5 => 'ssh_host_public_key_md5',
		CURLOPT_PROXY_TRANSFER_MODE     => 'proxy_transfer_mode',
		CURLOPT_ADDRESS_SCOPE           => 'address_scope',
		CURLOPT_CRLFILE                 => 'crlfile',
		CURLOPT_ISSUERCERT              => 'issuercert',
		CURLOPT_KEYPASSWD               => 'keypasswd',
		CURLOPT_CERTINFO                => 'certinfo',
		CURLOPT_PASSWORD                => 'password',
		CURLOPT_POSTREDIR               => 'postredir',
		CURLOPT_PROXYPASSWORD           => 'proxypassword',
		CURLOPT_PROXYUSERNAME           => 'proxyusername',
		CURLOPT_USERNAME                => 'username',
		CURLOPT_NOPROXY                 => 'noproxy',
		CURLOPT_PROTOCOLS               => 'protocols',
		CURLOPT_REDIR_PROTOCOLS         => 'redir_protocols',
		CURLOPT_SOCKS5_GSSAPI_NEC       => 'socks5_gssapi_nec',
		CURLOPT_SOCKS5_GSSAPI_SERVICE   => 'socks5_gssapi_service',
		CURLOPT_TFTP_BLKSIZE            => 'tftp_blksize',
		CURLOPT_SSH_KNOWNHOSTS          => 'ssh_knownhosts',
		CURLOPT_FTP_USE_PRET            => 'ftp_use_pret',
		CURLOPT_MAIL_FROM               => 'mail_from',
		CURLOPT_MAIL_RCPT               => 'mail_rcpt',
		CURLOPT_RTSP_CLIENT_CSEQ        => 'rtsp_client_cseq',
		CURLOPT_RTSP_REQUEST            => 'rtsp_request',
		CURLOPT_RTSP_SERVER_CSEQ        => 'rtsp_server_cseq',
		CURLOPT_RTSP_SESSION_ID         => 'rtsp_session_id',
		CURLOPT_RTSP_STREAM_URI         => 'rtsp_stream_uri',
		CURLOPT_RTSP_TRANSPORT          => 'rtsp_transport',
		CURLOPT_FNMATCH_FUNCTION        => 'fnmatch_function',
		CURLOPT_WILDCARDMATCH           => 'wildcardmatch',
		CURLOPT_RESOLVE                 => 'resolve',
		CURLOPT_TLSAUTH_PASSWORD        => 'tlsauth_password',
		CURLOPT_TLSAUTH_TYPE            => 'tlsauth_type',
		CURLOPT_TLSAUTH_USERNAME        => 'tlsauth_username',
		CURLOPT_ACCEPT_ENCODING         => 'accept_encoding',
		CURLOPT_TRANSFER_ENCODING       => 'transfer_encoding',
		CURLOPT_GSSAPI_DELEGATION       => 'gssapi_delegation',
		CURLOPT_ACCEPTTIMEOUT_MS        => 'accepttimeout_ms',
		CURLOPT_DNS_SERVERS             => 'dns_servers',
		CURLOPT_MAIL_AUTH               => 'mail_auth',
		CURLOPT_SSL_OPTIONS             => 'ssl_options',
		CURLOPT_TCP_KEEPALIVE           => 'tcp_keepalive',
		CURLOPT_TCP_KEEPIDLE            => 'tcp_keepidle',
		CURLOPT_TCP_KEEPINTVL           => 'tcp_keepintvl',
		//		CURLOPT_SASL_IR                 => 'sasl_ir',
		//		CURLOPT_DNS_INTERFACE           => 'dns_interface',
		//		CURLOPT_DNS_LOCAL_IP4           => 'dns_local_ip4',
		//		CURLOPT_DNS_LOCAL_IP6           => 'dns_local_ip6',
		//		CURLOPT_XOAUTH2_BEARER          => 'xoauth2_bearer',
		//		CURLOPT_LOGIN_OPTIONS           => 'login_options',
		//		CURLOPT_EXPECT_100_TIMEOUT_MS   => 'expect_100_timeout_ms',
		//		CURLOPT_SSL_ENABLE_ALPN         => 'ssl_enable_alpn',
		//		CURLOPT_SSL_ENABLE_NPN          => 'ssl_enable_npn',
		//		CURLOPT_HEADEROPT               => 'headeropt',
		//		CURLOPT_PROXYHEADER             => 'proxyheader',
		//		CURLOPT_PINNEDPUBLICKEY         => 'pinnedpublickey',
		//		CURLOPT_UNIX_SOCKET_PATH        => 'unix_socket_path',
		//		CURLOPT_SSL_VERIFYSTATUS        => 'ssl_verifystatus',
		//		CURLOPT_PATH_AS_IS              => 'path_as_is',
		//		CURLOPT_SSL_FALSESTART          => 'ssl_falsestart',
		//		CURLOPT_PIPEWAIT                => 'pipewait',
		//		CURLOPT_PROXY_SERVICE_NAME      => 'proxy_service_name',
		//		CURLOPT_SERVICE_NAME            => 'service_name',
		//		CURLOPT_DEFAULT_PROTOCOL        => 'default_protocol',
		//		CURLOPT_STREAM_WEIGHT           => 'stream_weight',
		//		CURLOPT_TFTP_NO_OPTIONS         => 'tftp_no_options',
		//		CURLOPT_CONNECT_TO              => 'connect_to',
		//		CURLOPT_TCP_FASTOPEN            => 'tcp_fastopen',
		CURLOPT_SAFE_UPLOAD             => 'safe_upload',
	];

	const POST_RAW = 'raw';
	const POST_FORM_URLENCODED = 'form-urlencoded';
	// const POST_FORM_DATA = 'form-data'; // 未支持
	const POST_JSON = 'json';

	const POST_JSON_UNESCAPED = 'json_unescaped';

	const FORM_DATA_EOL = "\r\n";

	const FORM_DATA_UPLOAD_DELIMITER = '-------------';

	const LOOP_FORM_DATA = 0;
	const LOOP_FILE = 1;

	const SYNC = 0;
	const ASYNC = 1;
	const ASYNC_ONCE = 2;

	const GET = 'get';

	const POST = 'post';

	const PUT = 'put';

	const DELETE = 'delete';

	/** @var Uri */
	protected $uri = null;

	protected $method = self::GET;

	protected $postBodyType = self::POST_RAW;

	protected $isUseHttp2 = false;

	protected $postData = [];

	protected $rawPostText = '';

	protected $isRawPost = false;

	protected $userAgent = 'Kephp-CurlRequest';

	// curl 根域名证书验证
	protected $cacert = '';

	protected $sslVerifyPeer = 0;

	protected $sslVerifyHost = 0;

	protected $headers = [];

	protected $asyncMode = 0;

	protected $encoding = '';

	protected $maxRedirs = 10;

	protected $timeOut = 0;

	protected $followLocation = false;

	protected $isCleanXSSData = true;

	protected $fetchResponseHeaders = false;

	protected $error = '';

	protected $errorno = 0;

	protected $isSend = false;

	protected $curl = null;

	private $curlMulti = null;

	protected $response = '';

	protected $responseHeaderSize = 0;

	protected $responseHeaders = '';

	protected $isSSlVerifyHost = 0;

	protected $isDebug = false;

	protected $info = null;

	protected $options = [];

	/**
	 * @param null $uri
	 *
	 * @return CurlRequest
	 */
	public static function new($uri = null)
	{
		return new static($uri);
	}

	/**
	 * @param $uri
	 *
	 * @return CurlRequest
	 */
	public static function http2($uri = null)
	{
		return (new static($uri))->useHttp2(true);
	}

	public static function isSupportHttp2()
	{
		return CURL_VERSION_HTTP2 !== 0;
	}

	public static function fixCurlOptConstants()
	{
		defined('CURLOPT_SASL_IR') || define('CURLOPT_SASL_IR', 218);
		defined('CURLOPT_DNS_INTERFACE') || define('CURLOPT_DNS_INTERFACE', 10221);
		defined('CURLOPT_DNS_LOCAL_IP4') || define('CURLOPT_DNS_LOCAL_IP4', 10222);
		defined('CURLOPT_DNS_LOCAL_IP6') || define('CURLOPT_DNS_LOCAL_IP6', 10223);
		defined('CURLOPT_XOAUTH2_BEARER') || define('CURLOPT_XOAUTH2_BEARER', 10220);
		defined('CURLOPT_LOGIN_OPTIONS') || define('CURLOPT_LOGIN_OPTIONS', 10224);
		defined('CURLOPT_EXPECT_100_TIMEOUT_MS') || define('CURLOPT_EXPECT_100_TIMEOUT_MS', 227);
		defined('CURLOPT_SSL_ENABLE_ALPN') || define('CURLOPT_SSL_ENABLE_ALPN', 226);
		defined('CURLOPT_SSL_ENABLE_NPN') || define('CURLOPT_SSL_ENABLE_NPN', 225);
		defined('CURLOPT_HEADEROPT') || define('CURLOPT_HEADEROPT', 229);
		defined('CURLOPT_PROXYHEADER') || define('CURLOPT_PROXYHEADER', 10228);
		defined('CURLOPT_PINNEDPUBLICKEY') || define('CURLOPT_PINNEDPUBLICKEY', 10230);
		defined('CURLOPT_UNIX_SOCKET_PATH') || define('CURLOPT_UNIX_SOCKET_PATH', 10231);
		defined('CURLOPT_SSL_VERIFYSTATUS') || define('CURLOPT_SSL_VERIFYSTATUS', 232);
		defined('CURLOPT_PATH_AS_IS') || define('CURLOPT_PATH_AS_IS', 234);
		defined('CURLOPT_SSL_FALSESTART') || define('CURLOPT_SSL_FALSESTART', 233);
		defined('CURLOPT_PIPEWAIT') || define('CURLOPT_PIPEWAIT', 237);
		defined('CURLOPT_PROXY_SERVICE_NAME') || define('CURLOPT_PROXY_SERVICE_NAME', 10235);
		defined('CURLOPT_SERVICE_NAME') || define('CURLOPT_SERVICE_NAME', 10236);
		defined('CURLOPT_DEFAULT_PROTOCOL') || define('CURLOPT_DEFAULT_PROTOCOL', 10238);
		defined('CURLOPT_STREAM_WEIGHT') || define('CURLOPT_STREAM_WEIGHT', 239);
		defined('CURLOPT_TFTP_NO_OPTIONS') || define('CURLOPT_TFTP_NO_OPTIONS', 242);
		defined('CURLOPT_CONNECT_TO') || define('CURLOPT_CONNECT_TO', 10243);
		defined('CURLOPT_TCP_FASTOPEN') || define('CURLOPT_TCP_FASTOPEN', 244);
	}

	public static function convertOptions(array $options)
	{
		$result = [];
		$method = 'get';
		if (isset($options[CURLOPT_CUSTOMREQUEST]))
			$method = $options[CURLOPT_CUSTOMREQUEST];
		elseif (!empty($options[CURLOPT_POST]))
			$method = 'post';

		foreach ($options as $key => $value) {
			if (isset(self::CURLOPT_KEY_MAPS[$key])) {
				$result[self::CURLOPT_KEY_MAPS[$key]] = $value;
			}
		}
		$result['method'] = $method;
		return $result;
	}

	public function __construct($uri = null)
	{
		$this->options = [];
		if (isset($uri))
			$this->setUri($uri);
	}

	/**
	 * @param $uri
	 *
	 * @return $this
	 */
	public function setUri($uri)
	{
		if (!($uri instanceof Uri))
			$uri = new Uri($uri);
		$this->uri = $uri;
		return $this;
	}

	/**
	 * @return Uri
	 */
	public function getUri(): Uri
	{
		return $this->uri;
	}

	public function getStringUri(): string
	{
		return $this->uri->toUri(true);
	}

	public function setDebug(bool $isDebug)
	{
		$this->isDebug = $isDebug;
		return $this;
	}

	public function isDebug()
	{
		return $this->isDebug;
	}

	public function getDebugInfo()
	{
		if ($this->isDebug && $this->isSend) {
			return $this->info;
		}
		return null;
	}

	public function getOptions(bool $isConvert = false)
	{
		if ($isConvert) {
			return static::convertOptions($this->options);
		}
		return $this->options;
	}

	public function setMethod(string $method)
	{
		$method = strtolower($method);
		if ($method === self::GET || $method === self::POST)
			$this->method = $method;
		return $this;
	}

	public function getMethod(): string
	{
		return strtolower($this->method);
	}

	public function isPost(): bool
	{
		return $this->getMethod() === self::POST;
	}

	public function isPostLike(): bool
	{
		$method = $this->getMethod();
		return $method === self::POST || $method === self::PUT || $method === self::DELETE;
	}

	public function setUserAgent(string $userAgent = null)
	{
		if (empty($userAgent))
			$userAgent = '';
		$this->userAgent = $userAgent;
		return $this;
	}

	public function getUserAgent(): string
	{
		return $this->userAgent;
	}

	public function setFetchResponseHeaders(bool $isFetch)
	{
		if (!$this->isSend) {
			$this->fetchResponseHeaders = $isFetch;
		}
		return $this;
	}

	public function isFetchResponseHeaders()
	{
		return $this->fetchResponseHeaders;
	}

	public function setAsync(int $mode)
	{
		if ($mode < self::ASYNC)
			$mode = self::SYNC;
		elseif ($mode > self::ASYNC)
			$mode = self::ASYNC_ONCE;
		$this->asyncMode = $mode;
		return $this;
	}

	public function isAsync()
	{
		return $this->asyncMode > self::SYNC;
	}

	public function setHeaders(array $headers)
	{
		foreach ($headers as $key => $header) {
			$this->headers[$key] = $header;
		}
		return $this;
	}

	public function makeOptions()
	{
		//@todo 暂时这样处理URI
		// $uri = $this->uri->toUri(true);
		// $uriData = explode('?', $uri);
		// if(!empty($uriData) && isset($uriData[0]) && isset($uriData[1])) {
		// 	$url = substr($uriData[0], 0, strlen($uriData[0]) - 1);
		// 	$newUrl = $url . '?' . $uriData[1];
		// } else {
		$newUrl = $this->uri->toUri(true);
		// }

		$options = [
			CURLOPT_URL            => $newUrl,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_ENCODING       => $this->encoding,
			CURLOPT_MAXREDIRS      => $this->maxRedirs,
			CURLOPT_TIMEOUT        => $this->timeOut,
			CURLOPT_FOLLOWLOCATION => $this->followLocation,
			CURLOPT_SSL_VERIFYPEER => false, // 信任任何证书
			CURLOPT_SSL_VERIFYHOST => $this->isSSlVerifyHost, // 检查证书中是否设置域名
		];

		if (!empty($this->userAgent))
			$options[CURLOPT_USERAGENT] = $this->userAgent;

		$headers = $this->headers;
		$isPostLike = 0;
		$method = $this->getMethod();
		switch ($method) {
			case self::PUT :
			case self::DELETE :
				$options[CURLOPT_CUSTOMREQUEST] = $method;
				$isPostLike = 2;
				break;
			case self::POST :
				$options[CURLOPT_POST] = 1;
				$isPostLike = 1;
				break;
		}

		if ($isPostLike > 0) {
			$body = '';
			if (!empty($this->rawPostText)) {
				$body = $this->rawPostText;
			} elseif (!empty($this->postData)) {
				$postData = $this->cleanXSSData($this->postData);
				if (!empty($postData)) {
					if ($this->isPostRaw()) {
						$body = http_build_query($postData);
					} elseif ($this->isPostJson()) {
						$jsonOptions = $this->isPostJsonUnescaped() ? (JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS) : 0;
						$body = json_encode($postData, $jsonOptions);
					}
				}
			}

			if ($this->isPostFormUrlencoded()) {
				$headers['Content-Type'] = 'application/x-www-form-urlencoded';
				$headers['Content-Length'] = strlen($body);
			} elseif ($this->isPostJson()) {
				$headers['Content-Type'] = 'application/json';
				$headers['Content-Length'] = strlen($body);
			}
			$options[CURLOPT_POSTFIELDS] = $body;
		}

		if (!empty($headers)) {
			$_headers = [];
			foreach ($headers as $key => $value) {
				if (!empty($key) && !is_numeric($key) && is_string($key)) {
					$_headers[] = $key . ': ' . $value;
				} else {
					$_headers[] = $value;
				}
			}
			$options[CURLOPT_HTTPHEADER] = $_headers;
		}

		if ($this->fetchResponseHeaders) {
			$options[CURLOPT_RETURNTRANSFER] = 1;
			$options[CURLOPT_HEADER] = 1;
		}

		return $options;
	}

	public function setCleanXSSData(bool $isUse)
	{
		$this->isCleanXSSData = $isUse;
		return $this;
	}

	public function cleanXSSData(array $data = null)
	{
		if ($this->isCleanXSSData) {
			$data = $data ?? [];
			if (!empty($data)) {
				$antiXSS = new AntiXSS();
				$data = $antiXSS->xss_clean($data);
			}
		}
		return $data;
	}

	protected function mergeSendData($data)
	{
		if (!empty($data)) {
			if (is_array($data)) {
				if ($this->isPostLike())
					$this->postData = $data + $this->postData;
				else {
					$data = $this->cleanXSSData($data);
					$this->uri->mergeQuery($data);
				}
			} elseif (is_string($data)) {
				if ($this->isPost()) {
					$this->rawPostText = $data;
				}
			}

		}
		return $this;
	}

	protected function resetError()
	{
		$this->error = '';
		$this->errorno = 0;
		return $this;
	}

	protected function beforeSend()
	{

	}

	protected function afterSend()
	{

	}

	public function send($data = null, string $postBodyType = self::POST_RAW)
	{
		$this->postBodyType = $postBodyType;
		$this->resetError()->mergeSendData($data);

		$this->options = $this->makeOptions();

		$this->response = ''; // 先重置了response
		$this->curl = curl_init();

		$this->beforeSend();

		curl_setopt_array($this->curl, $this->options);

		$this->response = curl_exec($this->curl);
		$this->isSend = true;

		if ($this->fetchResponseHeaders) {
			$this->responseHeaderSize = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
			$this->responseHeaders = substr($this->response, 0, $this->responseHeaderSize);
			$this->response = substr($this->response, $this->responseHeaderSize);
		}

		if ($this->isDebug) {
			$this->info = curl_getinfo($this->curl);
		}

		$this->error = curl_error($this->curl);
		$this->errorno = curl_errno($this->curl);

		$this->afterSend();

		curl_close($this->curl);
		return $this;
	}

	// 这个异步模式没有用，不要用
	// public function async(array $data = null)
	// {
	// 	if (!$this->isAsync())
	// 		$this->setAsync(self::ASYNC_ONCE);
	// 	return $this->send($data);
	// }

	public function isPostRaw()
	{
		return $this->postBodyType === self::POST_RAW || $this->postBodyType === self::POST_FORM_URLENCODED;
	}

	public function isPostJson()
	{
		return $this->postBodyType === self::POST_JSON || $this->postBodyType === self::POST_JSON_UNESCAPED;
	}

	public function isPostJsonUnescaped()
	{
		return $this->postBodyType === self::POST_JSON_UNESCAPED;
	}

	public function isPostFormUrlencoded()
	{
		return $this->postBodyType === self::POST_FORM_URLENCODED;
	}

	public function get(array $data = null)
	{
		$this->method = self::GET;
		return $this->send($data);
	}

	public function post(array $data = null, string $postBodyType = self::POST_RAW)
	{
		$this->method = self::POST;
		return $this->send($data, $postBodyType);
	}

	public function postRaw($rawText = null, string $postBodyType = self::POST_RAW)
	{
		$this->method = self::POST;
		return $this->send($rawText, $postBodyType);
	}

	public function delete($data = null)
	{
		$this->method = self::DELETE;
		return $this->send($data);
	}

	public function put($data = null)
	{
		$this->method = self::PUT;
		return $this->send($data);
	}

	public function getCurl()
	{
		return $this->curl;
	}

	public function getError()
	{
		if (!$this->isSend) {
			return 0;
		}
		if ($this->errorno > 0)
			return $this->errorno;
		return 0;
	}

	public function getResponse(bool $isTrim = true)
	{
		if ($isTrim)
			return trim($this->response);
		return $this->response;
	}

	public function getResponseHeaders()
	{
		return $this->responseHeaders;
	}

	public function getResponseAsJson()
	{
		$resp = trim($this->response);
		if (empty($resp)) return [];
		return json_decode($resp, true);
	}

	/**
	 * @return \Ke\Utils\Status
	 */
	public function getResponseAsStatus()
	{
		$json = $this->getResponseAsJson();
		$status = $json['status'] ?? false;
		$message = $json['message'] ?? '';
		$data = $json['data'] ?? [];
		if (!is_string($message))
			$message = '';
		if (!is_array($data))
			$data = [];
		return new Status(!empty($status), $message, $data);
	}

	public function setSSLVerifyHost(int $val)
	{
		return $this->isSSlVerifyHost = $val;
	}

	private function loopArray(
		int $type = self::LOOP_FORM_DATA,
		array $data = [],
		string $prefix = '',
		string $delimiter = '',
		array &$buffer = []
	)
	{
		foreach ($data as $field => $value) {
			$_field = empty($prefix) ? $field : "{$prefix}[$field]";
			if (is_object($value))
				$value = (array)$value;
			if (is_array($value)) {
				$this->loopArray($type, $value, $_field, $delimiter, $buffer);
			} else {
				$buffer[] = "--" . $delimiter;
				if ($type === self::LOOP_FORM_DATA) {
					$buffer[] = 'Content-Disposition: form-data; name="' . $_field . "\"" . self::FORM_DATA_EOL;
					$buffer[] = $value;
				} else {
					$path = real_file($value);
					if ($path === false) continue;
					if (isset($data['name']) && !empty($data['name']))
						$fileName = $data['name'];
					else
						$fileName = basename($path);
					$content = file_get_contents($path);
					$type = MimeType::detectFile($path, MimeType::FILE_MIME);
					$buffer[] = 'Content-Disposition: form-data; name="' . $_field . '"; filename="' . $fileName . '"';
					$buffer[] = 'Content-Type: ' . $type;
					$buffer[] = 'Content-Transfer-Encoding: binary' . self::FORM_DATA_EOL;
					$buffer[] = $content;
				}
			}
		}
		return $buffer;
	}

	private function makeUploadData($boundary, $postData, $files)
	{
		$buffer = [];
		$delimiter = self::FORM_DATA_UPLOAD_DELIMITER . $boundary;

		$this->loopArray(self::LOOP_FORM_DATA, $postData, '', $delimiter, $buffer);
		$this->loopArray(self::LOOP_FILE, $files, '', $delimiter, $buffer);

		$buffer[] = "--" . $delimiter . "--";

		$data = implode(self::FORM_DATA_EOL, $buffer);

//		echo $data;

		return $data;
	}

	public function uploadAsFormData(array $files, array $postData = [])
	{
		$boundary = uniqid();
		$delimiter = self::FORM_DATA_UPLOAD_DELIMITER . $boundary;
		$postData = $this->makeUploadData($boundary, $postData, $files);
		$this->setHeaders([
			'Content-Type'   => "multipart/form-data; boundary={$delimiter}",
			'Content-Length' => strlen($postData),
		]);
		return $this->postRaw($postData);
	}

	/**
	 * @param $isUse
	 *
	 * @return $this
	 */
	public function useHttp2($isUse)
	{
		if (static::isSupportHttp2()) {
			$this->isUseHttp2 = !empty($isUse);
		}
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUseHttp2()
	{
		return $this->isUseHttp2;
	}
}