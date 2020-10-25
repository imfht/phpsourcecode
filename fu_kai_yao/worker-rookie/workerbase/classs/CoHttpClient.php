<?php

namespace workerbase\classs;

use Swoole\Coroutine\Http\Client;
use Swoole\Coroutine\Channel;

/**
 * swoole协程http客户端，必须在回调或者协程中使用
 * Class CoHttpClient
 * @package fky\classs
 * @author fukaiyao 2019-12-26 13:58:35
 */
class CoHttpClient
{
    public $http_status_code = 0;

    /**
     * 请求的响应
     * @var null
     */
    public $response = null;

    public $response_headers = null;

    public $response_cookies = null;

    public $error = false;

    public $error_code = 0;

    public $error_message = null;

    public $client_error = false;

    public $client_error_code = 0;

    public $client_error_message = null;

    public $http_error = false;

    public $http_error_message = null;

    /**
     * http连接池
     * @var array
     */
    private $_httpPool = [];

    private $_client = [];

    private $_iskeep = 0;

    /**
     * 延迟收包的请求
     * @var array
     */
    private $_deferQueryArr = [];

    /**
     * 当前请求的host和path
     * @var string
     */
    private $_currentPath = [];
    public $_currentHost = [];

    public function __construct()
    {
        if (!extension_loaded('swoole')) {
            throw new \ErrorException('The swoole extensions is not loaded');
        }

        if (!class_exists('\Swoole\Coroutine')) {
            throw new \ErrorException('Swoole version does not support Coroutine, please use version 4.4 or above');
        }
    }

    /**
     * 预创建http请求
     * @param $url -地址
     * @param $method -请求的方法
     * @param array $data 请求的数据
     * @param int $cid  协程id(用于跨协程标记客户端)
     * @param bool $keepalive 是否长连接(用于同协程通道复用，跨协程调用请使用false)
     * @return $this|bool
     */
    public function createHttp($url, $method, $data = array(), $cid=0, $keepalive=false)
    {
        $arr = parse_url($url);
        if (empty($arr)) {
            return false;
        }

        $port = 80;
        $path = '';
        $query = '';
        $host = $arr['host'];
        $ssl = false;
        if (isset($arr['scheme']) && $arr['scheme'] == 'https') {
            $port = 443;
            $ssl = true;
        }
        if (isset($arr['port'])) {
            $port = $arr['port'];
        }
        if (isset($arr['path'])) {
            $path = $arr['path'];
        }
        if (isset($arr['query'])) {
            $query = $arr['query'];
        }

        if (strtolower($method) != 'post' && count($data) > 0) {
            $query = http_build_query($data) . '&' . $query;
        }

        if ($query) {
            $path = $path . '?' . $query;
        }

        $this->_currentPath[$cid] = $path;
        $this->_currentHost[$cid] = $host . ':' . $port;

        $this->initClient($host, $port, $ssl, $keepalive, $cid);
        $this->_client[$cid]->setMethod($method);

        if (strtolower($method) == 'post') {
            $this->_client[$cid]->setData($data);
        }

        return $this;
    }

    /**
     * get请求
     * @param $url
     * @param array $data
     * @param bool $keepalive 是否长连接(用于同协程通道复用，跨协程调用请使用false)
     * @param int $cid  协程id(用于跨协程标记客户端)
     * @return bool
     */
    public function get($url, $data = array(), $keepalive = false, $cid = 0)
    {
        if (!$this->createHttp($url,"GET", $data, $cid, $keepalive)) {
            return false;
        }

        return $this->exec($cid);
    }

    /**
     * post请求
     * @param $url
     * @param array $data
     * @param bool $keepalive 是否长连接(用于同协程通道复用，跨协程调用请使用false)
     * @param int $cid  协程id(用于跨协程标记客户端)
     * @return bool
     */
    public function post($url, $data = array(), $keepalive = false, $cid = 0)
    {
        if (!$this->createHttp($url,"POST", $data, $cid, $keepalive)) {
            return false;
        }

        return $this->exec($cid);
    }

    /**
     * 初始化客户端连接
     * @param $host
     * @param $port
     * @param bool $ssl 是否https信道加密
     * @param bool $keepalive 是否长连接(用于同协程通道复用，跨协程调用请使用false)
     * @param int $cid  协程id(用于跨协程标记客户端)
     */
    public function initClient($host, $port, $ssl=false, $keepalive=false, $cid = 0)
    {
        $isFind = 0;
        //先查找连接池
        if (isset($this->_httpPool[$host . ':' . $port])) {
//            if ($this->_client[$cid] = $this->_httpPool[$host . ':' . $port]->pop(1)) {
//                echo '连接复用';
//                $isFind = 1;
//                if (!$this->_client[$cid]->connected) {
//                    $isFind = 0;
//                }
//            }

            if (!$this->_httpPool[$host . ':' . $port]->isEmpty()) {
//                echo '连接复用';
                $isFind = 1;
                $this->_client[$cid] = $this->_httpPool[$host . ':' . $port]->pop(1);
                if (!$this->_client[$cid] || !$this->_client[$cid]->connected) {
                    $isFind = 0;
                }
            }

        }

        //连接池中没有
        if (!$isFind) {
//            echo '新建连接';
            $this->_client[$cid] = new Client($host, $port, $ssl);
            $this->setKeepAlive($keepalive, $cid);
            //长连接初始化连接池
            if ($keepalive && !isset($this->_httpPool[$host . ':' . $port])) {
//                echo '创建池';
                $this->_httpPool[$host . ':' . $port] = new Channel(2);
            }
        }
    }

    public function setHeader(array $headers, $cid = 0)
    {
        $this->_client[$cid]->setHeaders($headers);
        return $this;
    }

    public function setUserAgent($useragent, $cid = 0)
    {
        $this->_client[$cid]->setHeaders(['User-Agent' => $useragent]);
        return $this;
    }

    public function setCookie(array $cookies, $cid = 0)
    {
        $this->_client[$cid]->setCookies($cookies);
        return $this;
    }

    public function setTimeout($sec, $cid = 0)
    {
        $this->_client[$cid]->set(['timeout' => $sec]);
        return $this;
    }

    //开启长连接
    public function setKeepAlive($open = true, $cid = 0)
    {
        $this->_client[$cid]->set(['keep_alive' => $open]);
        return $this;
    }

    /**
     * 延迟发包
     * @param int $cid  协程id(用于标记客户端)
     * @param int $isRecv  是否需要收包
     * @return $this
     */
    public function setDefer($cid = 0, $isRecv = 1)
    {
        $this->_client[$cid]->setDefer();
        if ($isRecv) {
            $this->_deferQueryArr[$this->_currentHost[$cid]][$cid] = $this->_client[$cid];
        }
        return $this;
    }

    //批量收包（setDefer()方法后使用）
    public function recvAll()
    {
        $result = array();
        foreach ($this->_deferQueryArr as $host => $clients) {
            foreach ($clients as $cid => $client) {
                $client->recv();
                $result[] = $client->body;
                unset($this->_deferQueryArr[$host][$cid]);
            }
            if (isset($this->_httpPool[$host])) {
                $this->_httpPool[$host]->close();
                unset($this->_httpPool[$host]);
            }
        }

        return $result;
    }

    /**
     * 执行http请求
     * @param int $cid  协程id(用于标记客户端)
     * @return bool
     */
    public function exec($cid = 0)
    {
        $res = $this->_client[$cid]->execute($this->_currentPath[$cid]);
        if (!isset($this->_deferQueryArr[$this->_currentHost[$cid]][$cid]) && $this->_client[$cid]->setting['keep_alive'] && isset($this->_httpPool[$this->_currentHost[$cid]])) {
//            echo '放回池';
            $result = $this->_httpPool[$this->_currentHost[$cid]]->push($this->_client[$cid], 1);
//            var_dump('回',$result);
//            var_dump($this->_httpPool[$this->_currentHost[$cid]]->stats());

        }
        $this->initResponse($cid);
        unset($this->_client[$cid]);
        return $res;
    }

    //初始化响应
    protected function initResponse($cid = 0)
    {
        $this->response = $this->_client[$cid]->body;
        $this->client_error_code = $this->_client[$cid]->errCode;
        $this->client_error_message = socket_strerror($this->client_error_code);
        $this->client_error = !($this->client_error_code === 0);
        $this->http_status_code = $this->_client[$cid]->statusCode;
        $this->http_error = in_array(floor($this->http_status_code / 100), array(
            4,
            5
        ));
        $this->error = $this->client_error || $this->http_error;
        $this->error_code = $this->error ? ($this->client_error ? $this->client_error_code : $this->http_status_code) : 0;
        $this->response_headers = $this->_client[$cid]->headers;
        $this->response_cookies = $this->_client[$cid]->cookies;
        $this->http_error_message = $this->error ? $this->response : '';
        $this->error_message = $this->client_error ? $this->client_error_message : $this->http_error_message;
    }


    /**
     * 关闭连接
     * @param int $cid  协程id(用于标记客户端)
     * @return $this
     */
    public function close($cid = 0)
    {
        $this->_client[$cid]->close();
        if (isset($this->_deferQueryArr[$this->_currentHost[$cid]][$cid])) {
            unset($this->_deferQueryArr[$this->_currentHost[$cid]][$cid]);
        }
        return $this;
    }

    public function __destruct()
    {
        foreach ($this->_httpPool as $host => $client) {
            $client->close();
            unset($this->_httpPool[$host]);
        }
    }

    /**
     * Was an 'info' header returned.
     *
     * @return bool
     */
    public function isInfo()
    {
        return $this->http_status_code >= 100 && $this->http_status_code < 200;
    }

    /**
     * Was an 'OK' response returned.
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->http_status_code >= 200 && $this->http_status_code < 300;
    }

    /**
     * Was a 'redirect' returned.
     *
     * @return bool
     */
    public function isRedirect()
    {
        return $this->http_status_code >= 300 && $this->http_status_code < 400;
    }

    /**
     * Was an 'error' returned (client error or server error).
     *
     * @return bool
     */
    public function isError()
    {
        return $this->http_status_code >= 400 && $this->http_status_code < 600;
    }

    /**
     * Was a 'client error' returned.
     *
     * @return bool
     */
    public function isClientError()
    {
        return $this->http_status_code >= 400 && $this->http_status_code < 500;
    }

    /**
     * Was a 'server error' returned.
     *
     * @return bool
     */
    public function isServerError()
    {
        return $this->http_status_code >= 500 && $this->http_status_code < 600;
    }

}