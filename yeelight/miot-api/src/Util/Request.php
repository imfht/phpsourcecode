<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-11
 * Time: 下午2:28.
 */

namespace MiotApi\Util;

class Request
{
    protected $hasCurl;
    protected $useCurl;
    protected $useBasicAuth;
    protected $HTTPVersion;
    protected $host;
    protected $port;
    protected $url;
    protected $uri;
    protected $headers;
    protected $data;
    protected $type;
    protected $query;
    protected $timeout;
    protected $error;
    protected $response;
    protected $responseText;
    protected $responseHeaders;
    protected $executed;
    protected $fsock;
    protected $curl;
    protected $additionalCurlOpts;
    protected $authUsername;
    protected $authPassword;

    /**
     * Request constructor.
     *
     * @param string $host
     * @param string $uri
     * @param int    $port
     * @param bool   $useCurl
     * @param int    $timeout
     */
    public function __construct($host = '', $uri = '/', $port = 80, $useCurl = true, $timeout = 10)
    {
        if (!$host) {
            return false;
        }
        $this->hasCurl = function_exists('curl_init');
        $this->useCurl = $this->hasCurl ? ($useCurl ? $useCurl : false) : false;
        $this->type = 'GET';
        $this->HTTPVersion = '1.1';
        $this->host = $host ? $host : $_SERVER['HTTP_HOST'];
        $this->uri = $uri;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->setHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
        $this->setHeader('Accept-Language', 'en-us,en;q=0.5');
        $this->setHeader('Accept-Encoding', 'deflate');
        $this->setHeader('Accept-Charset', 'ISO-8859-1,utf-8;q=0.7,*;q=0.7');
        $this->setHeader('User-Agent', 'Mozilla/5.0 Firefox/3.6.12');
        $this->setHeader('Connection', 'close');

        return $this;
    }

    /**
     * 设置header头.
     *
     * @param $header
     * @param $content
     *
     * @return $this
     */
    public function setHeader($header, $content)
    {
        $this->headers[$header] = $content;

        return $this;
    }

    public static function curlHeaders()
    {
        return [
            'User-Agent' => CURLOPT_USERAGENT,
        ];
    }

    /**
     * 设置请求的host.
     *
     * @param $host
     *
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * 设置请求的URI.
     *
     * @param $uri
     *
     * @return $this
     */
    public function setRequestURI($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * 设置请求的端口.
     *
     * @param $port
     *
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * 设置请求的超时时间.
     *
     * @param $timeout
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * 设置请求的参数.
     *
     * @param $get
     *
     * @return $this
     */
    public function setQueryParams($get)
    {
        $this->query = $get;

        return $this;
    }

    /**
     * 设置是否使用curl.
     *
     * @param $use
     *
     * @return $this
     */
    public function setUseCurl($use)
    {
        if ($use && $this->hasCurl) {
            $this->useCurl = true;
        } else {
            $this->useCurl = false;
        }

        return $this;
    }

    /**
     * 设置请求类型.
     *
     * @param $type
     *
     * @return $this
     */
    public function setType($type)
    {
        if (in_array($type, ['POST', 'GET', 'PUT', 'DELETE'])) {
            $this->type = $type;
        }

        return $this;
    }

    /**
     * 设置数据.
     *
     * @param $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * 设置请求的url.
     *
     * @param $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * 设置curl的参数.
     *
     * @param $option
     * @param $value
     */
    public function setAdditionalCurlOpt($option, $value)
    {
        if (is_array($option)) {
            foreach ($option as $opt => $val) {
                $this->setAdditionalCurlOpt($opt, $val);
            }
        } else {
            $this->additionalCurlOpts[$option] = $value;
        }
    }

    /**
     * 设置认证信息.
     *
     * @param $set
     * @param string | null $username
     * @param string | null $password
     */
    public function setUseBasicAuth($set, $username = null, $password = null)
    {
        $this->useBasicAuth = $set;
        if ($username) {
            $this->setAuthUsername($username);
        }
        if ($password) {
            $this->setAuthPassword($password);
        }
    }

    public function setAuthUsername($username = null)
    {
        $this->authUsername = $username;
    }

    public function setAuthPassword($password = null)
    {
        $this->authPassword = $password;
    }

    public function execute()
    {
        if ($this->useCurl) {
            $this->curlExecute();
        } else {
            $this->fsockgetExecute();
        }

        return $this;
    }

    /**
     * curl请求方式.
     */
    protected function curlExecute()
    {
        $uri = $this->uri;
        $host = $this->host;
        $type = $this->type;
        $port = $this->port;
        $data = property_exists($this, 'data') ? $this->param($this->data) : false;
        //$timeout = $this->timeout;
        // Initiate cURL.
        $ch = curl_init();
        // Set request type.
        if ($type === 'GET') {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } elseif ($type === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
        } elseif ($type === 'PUT') {
            //curl_setopt($ch, CURLOPT_PUT, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        }
        // Grab query string.
        $query = property_exists($this, 'query') && $this->query ? '?'.$this->param($this->query) : '';
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Set additional headers.
        $headers = [];
        foreach ($this->headers as $name => $val) {
            $headers[] = $name.': '.$val;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // Do stuff it it's HTTPS/SSL.
        if ($port == 443) {
            $protocol = 'https';
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        } else {
            $protocol = 'http';
        }
        if (!empty($this->additionalCurlOpts)) {
            foreach ($this->additionalCurlOpts as $option => $value) {
                curl_setopt($ch, $option, $value);
            }
        }
        // Build and set URL.
        $url = $protocol.'://'.$host.$uri.$query;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT, $port);
        // Add any authentication to the request.
        // Currently supports only HTTP Basic Auth.
        if ($this->useBasicAuth === true) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->authUsername.':'.$this->authPassword);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }
        // Execute!
        $rsp = curl_exec($ch);

        $this->curl = $ch;
        $this->executed = true;

        // Handle an error.
        if (!$error = curl_error($ch)) {
            $this->response = ['responseText' => $rsp] + curl_getinfo($ch);
            $this->responseHeaders = curl_getinfo($ch);
            $this->responseText = $rsp;
        } else {
            $this->error = $error;
        }
    }

    /**
     * @param $data
     *
     * @return string
     */
    public function param($data)
    {
        $dataArray = [];

        if (!empty($data)) {
            foreach ($data as $key => $val) {
                if (!is_string($val)) {
                    $val = json_encode($val);
                }
                $dataArray[] = urlencode($key).'='.urlencode($val);
            }
        }

        return implode('&', $dataArray);
    }

    /**
     * fsock请求方式.
     */
    protected function fsockgetExecute()
    {
        $uri = $this->uri;
        $host = $this->host;
        $port = $this->port;
        $type = $this->type;
        $HTTPVersion = $this->HTTPVersion;
        $data = property_exists($this, 'data') ? $this->data : null;
        $crlf = "\r\n";

        $rsp = '';

        // Deal with the data first.
        if ($data && $type === 'POST') {
            $data = $this->param($data);
        } elseif ($data && $type === 'GET') {
            $get_data = $data;
            $data = $crlf;
        } else {
            $data = $crlf;
        }
        // Then add
        if ($type === 'POST') {
            $this->setHeader('Content-Type', 'application/x-www-form-urlencoded');
            $this->setHeader('Content-Length', strlen($data));
            $get_data = property_exists($this, 'query') && $this->query ? self::param($this->query) : false;
        } else {
            $this->setHeader('Content-Type', 'text/plain');
            $this->setHeader('Content-Length', strlen($crlf));
        }
        if ($type === 'GET') {
            if (isset($get_data)) {
                $get_data = $data;
            } elseif ($this->query) {
                $get_data = self::param($this->query);
            }
        }
        if ($this->useBasicAuth === true) {
            $this->setHeader(
                'Authorization',
                'Basic '.base64_encode($this->authUsername.':'.$this->authPassword)
            );
        }
        $headers = $this->headers;
        $req = '';
        $req .= $type.' '.$uri.(isset($get_data) ? '?'.$get_data : '').' HTTP/'.$HTTPVersion.$crlf;
        $req .= 'Host: '.$host.$crlf;
        foreach ($headers as $header => $content) {
            $req .= $header.': '.$content.$crlf;
        }
        $req .= $crlf;
        if ($type === 'POST') {
            $req .= $data;
        } else {
            $req .= $crlf;
        }

        // Construct hostname.
        $fsock_host = ($port == 443 ? 'ssl://' : '').$host;

        // Open socket.
        $httpreq = @fsockopen($fsock_host, $port, $errno, $errstr, 30);

        // Handle an error.
        if (!$httpreq) {
            $this->error = $errno.': '.$errstr;

            return false;
        }

        // Send the request.
        fwrite($httpreq, $req);

        // Receive the response.
        while ($line = fgets($httpreq)) {
            $rsp .= $line;
        }

        // Extract the headers and the responseText.
        list($headers, $responseText) = explode($crlf.$crlf, $rsp);

        // Store the finalized response.
        $this->response = $rsp;
        $this->responseText = $responseText;
        $this->status = array_shift($headers);

        // Store the response headers.
        $headers = explode($crlf, $headers);
        $this->responseHeaders = [];
        foreach ($headers as $header) {
            list($key, $val) = explode(': ', $header);
            $this->responseHeaders[$key] = $val;
        }

        // Mark as executed.
        $this->executed = true;

        // Store the resource so we can close it later.
        $this->fsock = $httpreq;
    }

    public function close()
    {
        if (!$this->executed) {
            return false;
        }
        if ($this->useCurl) {
            $this->curlClose();
        } else {
            $this->fsockgetClose();
        }
    }

    protected function curlClose()
    {
        curl_close($this->curl);
    }

    protected function fsockgetClose()
    {
        fclose($this->fsock);
    }

    public function getError()
    {
        return $this->error;
    }

    public function getResponse()
    {
        if (!$this->executed) {
            return false;
        }

        return $this->response;
    }

    public function getResponseText()
    {
        if (!$this->executed) {
            return false;
        }

        return $this->responseText;
    }

    public function getAllResponseHeaders()
    {
        if (!$this->executed) {
            return false;
        }

        return $this->responseHeaders;
    }

    public function getResponseHeader($header)
    {
        if (!$this->executed) {
            return false;
        }
        $headers = $this->responseHeaders;
        if (array_key_exists($header, $headers)) {
            return $headers[$header];
        }
    }
}
