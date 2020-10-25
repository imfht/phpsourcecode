<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Network;
\app()->import('Unirest', VENDOR_ROOT . '/unirest/src');
class_alias('\\Unirest\\Request', __NAMESPACE__ . '\\UniRequest');

use \Cute\Utility\IP;
use \Cute\Log\LoggerInterface;


/**
 * cURL的HTTP客户端
 * NOTICE:
 *   PHP的cURL无法看到本地的/etc/hosts文件，而Bash的curl可以
 */
class cURL
{
    use \Cute\Base\Deferring;
    use \Cute\Log\LoggerAwareTrait;

    const ERRNO_RESOLVE_FAIL = 6;
    const ERRNO_CONN_FAIL = 7;
    const ERRNO_DNS_FAIL = 28;

    protected $base_url = '';
    protected $global_opts = []; //备份全局options

    public function __construct($base_url = '', LoggerInterface $logger = null)
    {
        $this->setBaseURL($base_url);
        if ($logger) {
            $this->setLogger($logger);
        }
    }

    public function setBaseURL($base_url)
    {
        $this->base_url = rtrim($base_url, '/');
        return $this;
    }

    public function close()
    {
        unset($this->logger);
    }

    public function __call($name, $args)
    {
        $method = self::getRequestMethod($name);
        if (!$method) {
            return exec_method_array(UniRequest, $name, $args);
        }
        $this->prepare();
        @list($url, $headers, $body) = $args;
        if (!empty($this->base_url)) {
            $url = $args[0] = $this->getURLString($url);
        }
        $try_times = 1;
        do {
            $phrase = '';
            try {
                $result = exec_method_array(__NAMESPACE__ . '\\UniRequest', $name, $args);
            } catch (\Exception $e) {
                $phrase = $e->getMessage();
                if (!isset($headers['Host']) && self::isUnreachable()) {
                    $headers['Host'] = self::insteadOfURL($url);
                    array_splice($args, 0, 2, [$url, $headers]);
                    continue; //重试一次
                }
            }
            $try_times --;
        } while ($try_times > 0);
        $body = self::getBodyString($body);
        $this->finish($result, $method, $url, $body, $headers, $phrase);
        return $result;
    }

    public function getURLString($url)
    {
        $url = $this->base_url . '/' . ltrim($url, '/');
        return $url;
    }

    public static function insteadOfURL(& $url)
    {
        $hostname = parse_url($url, PHP_URL_HOST);
        if ($ipaddr = IP::getHostIP($hostname)) {
            $url = str_ireplace($hostname, $ipaddr, $url);
        }
        return $hostname;
    }

    public static function getRequestMethod($method = 'GET')
    {
        return @constant('\\Unirest\\Method::' . strtoupper($method));
    }

    public static function getBodyString($body)
    {
        if (empty($body)) {
            $body = '-';
        } else if (is_array($body) || $body instanceof \Traversable) {
            $body = UniRequest::buildHTTPCurlQuery($body);
            $body = http_build_query($body);
        }
        return $body;
    }

    public static function getErrorNO()
    {
        if ($handler = UniRequest::getCurlHandle()) {
            return curl_errno($handler);
        }
    }

    public static function isUnreachable()
    {
        $failures = [
            self::ERRNO_RESOLVE_FAIL,
            self::ERRNO_DNS_FAIL,
            self::ERRNO_CONN_FAIL,
        ];
        if ($errno = self::getErrorNO()) {
            return in_array($errno, $failures, true);
        }
        return false;
    }

    /**
     * 加入options
     */
    public function prepare(array $options = [])
    {
        if (!array_key_exists('timeout', $options)
            && !array_key_exists('Timeout', $options)
        ) {
            $options['Timeout'] = intval(ini_get('default_socket_timeout'));
        }
        if (!array_key_exists('useragent', $options)
            && !array_key_exists('UserAgent', $options)
        ) {
            $options['UserAgent'] = 'Mozilla/4.0';
        }
        if (empty($this->global_opts)) { //未保存过
            $this->global_opts = UniRequest::curlOpts([]);
        }
        if (!empty($options)) {
            UniRequest::curlOpts($this->global_opts);
        }
        return $this;
    }

    /**
     * 还原options和记录日志
     */
    public function finish(& $response, $method = 'GET', $url = '-', $reqbody = '-', $headers = [], $phrase = '')
    {
        UniRequest::clearCurlOpts();
        UniRequest::curlOpts($this->global_opts);
        if ($this->logger instanceof LoggerInterface) {
            if ($response) {
                $url = UniRequest::getInfo(CURLINFO_EFFECTIVE_URL);
                $connect_time = UniRequest::getInfo(CURLINFO_CONNECT_TIME);
                $total_time = UniRequest::getInfo(CURLINFO_TOTAL_TIME);
                $code = $response->code;
                $resbody = $response->body ?: '-';
            }
            $headers = empty($headers) ? "" : json_encode($headers) . "\n";
            $phrase .= ($phrase ? "\n" : "");
            $this->logger->info("{$method} \"{$url}\" {$connect_time} {$total_time} {$code}"
                . "\n{$headers}{$phrase}>>>>>>>>\n{$reqbody}\n<<<<<<<<\n{$resbody}\n");
        }
    }
}
