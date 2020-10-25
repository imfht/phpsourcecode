<?php

namespace inhere\gearman\tools;

/**
 * CurlHelper
 */
class CurlHelper
{
    /**
     * Can to retry
     * @var array
     */
    private static $canRetryErrorCodes = [
        CURLE_COULDNT_RESOLVE_HOST,
        CURLE_COULDNT_CONNECT,
        CURLE_HTTP_NOT_FOUND,
        CURLE_READ_ERROR,
        CURLE_OPERATION_TIMEOUTED,
        CURLE_HTTP_POST_ERROR,
        CURLE_SSL_CONNECT_ERROR,
    ];

    /**
     * @var int
     */
    private $errNo;

    /**
     * @var string
     */
    private $error;

    /**
     * @var array
     */
    private $info = [];

    /**
     * @var array
     */
    private $config = [
        'base_url' => '',
        'timeout' => 30,
        'retry' => 3,

        'proxy_host' => '',
        'proxy_port' => '',
    ];

    /**
     * @param array $config
     * @return CurlHelper
     */
    public static function make(array $config = [])
    {
        return new self($config);
    }

    /**
     * __construct
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * GET
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return array|mixed
     */
    public function get($url, array $data = [], array $headers = [])
    {
        if ($param = http_build_query($data)) {
            $url .= (strpos($url, '?') ? '&' : '?') . $param;
        }

        return $this->exec(
            $this->createCurl($url, 'GET', null, $headers)
        );
    }

    /**
     * POST
     * @param string $url 地址
     * @param array|string $data 数据
     * @param array $headers
     * @return mixed
     */
    public function post($url, $data = null, array $headers = [])
    {
        return $this->exec(
            $this->createCurl($url, 'POST', $data, $headers)
        );
    }

    /**
     * PUT
     * @param string $url 地址
     * @param array|string $data 数据
     * @param array $headers
     * @return mixed
     */
    public function put($url, $data = null, array $headers = [])
    {
        return $this->exec(
            $this->createCurl($url, 'PUT', $data, $headers)
        );
    }

    /**
     * DELETE
     * @param string $url 地址
     * @param array|string $data 数据
     * @param array $headers
     * @return mixed
     */
    public function delete($url, $data = null, array $headers = [])
    {
        return $this->exec(
            $this->createCurl($url, 'DELETE', $data, $headers)
        );
    }

    /**
     * @param $url
     * @param $method
     * @param mixed $data
     * @param array $headers
     * @return resource
     */
    public function createCurl($url, $method = 'GET', $data = null, array $headers = [])
    {
        $ch = curl_init();
        $url = $this->buildUrl($url);

        $method = strtoupper($method);
        switch ($method) {
            case 'GET':
                $curlOptions[CURLOPT_HTTPGET] = true;
                $curlOptions[CURLOPT_CUSTOMREQUEST] = $method;
                break;
            case 'POST':
                $curlOptions[CURLOPT_POST] = true;
                break;
            default:
                $curlOptions[CURLOPT_CUSTOMREQUEST] = $method;
        }

        if ($data === null) {
            if ($method === 'HEAD') {
                $curlOptions[CURLOPT_NOBODY] = true;
            }
        } else {
            $curlOptions[CURLOPT_POSTFIELDS] = $data;
        }

        $curlOptions[CURLOPT_URL] = $url;
        // 要求返回结果而不是输出
        $curlOptions[CURLOPT_RETURNTRANSFER] = true;
        //设置超时
        $curlOptions[CURLOPT_TIMEOUT] = $this->config['timeout'];
        $curlOptions[CURLOPT_CONNECTTIMEOUT] = $this->config['timeout'];

        $curlOptions[CURLOPT_ENCODING] = 'gzip'; // gzip

        $curlOptions[CURLOPT_FOLLOWLOCATION] = true;
        $curlOptions[CURLOPT_MAXREDIRS] = 5;

        // headers
        $headers[] = 'Expect: '; // 首次速度非常慢 解决
        $headers[] = 'Accept-Encoding: gzip, deflate'; // gzip
        $curlOptions[CURLOPT_HTTPHEADER] = $headers;

        // 如果有配置代理这里就设置代理
        if ($this->config['proxy_host'] && $this->config['proxy_port'] > 0) {
            $curlOptions[CURLOPT_PROXY] = $this->config['proxy_host'];
            $curlOptions[CURLOPT_PROXYPORT] = $this->config['proxy_port'];
        }

        $curlOptions[CURLOPT_SSL_VERIFYPEER] = false;
        $curlOptions[CURLOPT_SSL_VERIFYHOST] = false;

        // 首次速度非常慢 解决
        $curlOptions[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;

        // 设置不返回header 返回的响应就只有body
        $curlOptions[CURLOPT_HEADER] = false;

        foreach ($curlOptions as $option => $value) {
            curl_setopt($ch, $option, $value);
        }

        return $ch;
    }

    /**
     * @param $ch
     * @return mixed
     */
    public function exec($ch)
    {
        $ret = false;
        $retries = (int)$this->config['retry'];
        $retries = $retries > 20 || $retries < 0 ? 3 : $retries;

        while ($retries >= 0) {
            if (($ret = curl_exec($ch)) === false) {
                $curlErrNo = curl_errno($ch);

                if (false === in_array($curlErrNo, self::$canRetryErrorCodes, true) || !$retries) {
                    $curlError = curl_error($ch);

                    $this->errNo = $curlErrNo;
                    $this->error = sprintf('Curl error (code %s): %s', $this->errNo, $curlError);
                }

                $retries--;

                continue;
            }

            break;
        }

        $this->info = curl_getinfo($ch);

        curl_close($ch);

        return $ret;
    }

    /**
     * @param string $url
     * @return string
     */
    protected function buildUrl($url)
    {
        $url = trim($url);
        $baseUrl = $this->config['base_url'];

        // is a url part.
        if (!$this->isFullUrl($url)) {
            $url = $baseUrl . $url;
        }

        // check again
        if (!$this->isFullUrl($url)) {
            throw new \RuntimeException("The request url is not full, Url: $url");
        }

        return $url;
    }

    /**
     * @param $url
     * @return bool
     */
    public function isFullUrl($url)
    {
        return 0 === strpos($url, 'http:') || 0 === strpos($url, 'https:') || 0 === strpos($url, '//');
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);

        return $this;
    }

    /**
     * @return int
     */
    public function getErrNo()
    {
        return $this->errNo;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setBaseUrl($url)
    {
        $this->config['base_url'] = trim($url);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->config['base_url'];
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * isOk
     * @return boolean
     */
    public function isOk()
    {
        return !$this->error;
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return isset($this->info['http_code']) ? $this->info['http_code'] : 200;
    }

    /**
     * @return int
     */
    public function getConnectTime()
    {
        return isset($this->info['connect_time']) ? $this->info['connect_time'] : 0;
    }

    /**
     * @return int
     */
    public function getTotalTime()
    {
        return isset($this->info['total_time']) ? $this->info['total_time'] : 0;
    }
}
