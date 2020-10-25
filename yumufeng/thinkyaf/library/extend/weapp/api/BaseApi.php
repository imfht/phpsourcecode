<?php
/**
 * Created by PhpStorm.
 * User: yingouqlj
 * Date: 17/1/13
 * Time: 下午5:13
 */

namespace extend\weapp\api;

use Exception;
use extend\weapp\Config;
use extend\weapp\tool\CurlRequest;

class BaseApi
{
    const API = '';
    /**
     * NEED_ACCESS_TOKEN 是否需要 accessToken，如果为true，会掉接口获取
     */
    const NEED_ACCESS_TOKEN = false;
    protected $appId;
    protected $secret;
    protected $accessToken;
    protected $config;
    /**
     * 如果为true 直接返回curl结果
     */
    const CURL_RAW = false;
    /**
     * 返回结果映射，如果返回的字段看着别扭，定义下mapping
     */
    protected  static $result_mapping = [
    ];

    public function __construct(Config $config)
    {
        $this->appId = $config->appId;
        $this->secret = $config->secret;
        $this->config=$config;
        return $this;
    }

    public function setAccessToken($token)
    {
        $this->accessToken = $token;
    }

    protected function query($url, $params, $method = 'get')
    {
        $curl = false;
        switch ($method) {
            case 'get':
                $curl = CurlRequest::instance($url . '?' . http_build_query($params))
                    ->exec();
                break;
            case 'post':

                $curl = CurlRequest::instance($url)
                    ->setOption(CURLOPT_POST, 1)
                    ->setHeader('Content-Type', 'application/json; charset=utf-8')
                    ->setPostField(
                        json_encode($params)
                    )->setHeader('Content-Length', strlen(json_encode($params)))
                    ->exec();
                break;

        }
        if (!$curl[0]) {
            throw new Exception('none');
        }
        if (static::CURL_RAW == true) {
            return $curl[0];
        }
        try {
            $obj = json_decode($curl[0]);
            return $obj;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $params
     * @return static
     */
    protected function get($params)
    {
        $url = static::API . '?' . http_build_query($params);
        $curl = CurlRequest::instance($url)
            ->exec();
        return $this->buildResponse($curl);
    }

    /**
     * @param $params
     * @param array $urlParams
     * @return static
     */
    protected function post($params, $urlParams = [])
    {
        if (!empty($urlParams)) {
            $url = static::API . '?' . http_build_query($urlParams);
        } else {
            $url = static::API;
        }
        $curl = CurlRequest::instance($url)
            ->setOption(CURLOPT_POST, 1)
            ->setHeader('Content-Type', 'application/json; charset=utf-8')
            ->setPostField(
                json_encode($params)
            )->setHeader('Content-Length', strlen(json_encode($params)))
            ->exec();
        return $this->buildResponse($curl);
    }

    /**
     * 构建请求
     * @param $url
     * @param array $params
     * @param int $expire
     * @param array $extend
     * @return array
     */
    protected function makeRequest($url, $params = array(), $expire = 0, $extend = array())
    {
        if (empty($url)) {
            return array('code' => '100');
        }

        $_curl = curl_init();
        $_header = array(
            'Accept-Language: zh-CN',
            'Connection: Keep-Alive',
            'Cache-Control: no-cache'
        );

        // 只要第二个参数传了值之后，就是POST的
        if (!empty($params)) {
            curl_setopt($_curl, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($_curl, CURLOPT_POST, true);
        }

        if (substr($url, 0, 8) == 'https://') {
            curl_setopt($_curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($_curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        curl_setopt($_curl, CURLOPT_URL, $url);
        curl_setopt($_curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($_curl, CURLOPT_USERAGENT, 'API PHP CURL');
        curl_setopt($_curl, CURLOPT_HTTPHEADER, $_header);

        if ($expire > 0) {
            curl_setopt($_curl, CURLOPT_TIMEOUT, $expire); // 处理超时时间
            curl_setopt($_curl, CURLOPT_CONNECTTIMEOUT, $expire); // 建立连接超时时间
        }

        // 额外的配置
        if (!empty($extend)) {
            curl_setopt_array($_curl, $extend);
        }

        $result['result'] = curl_exec($_curl);
        $result['code'] = curl_getinfo($_curl, CURLINFO_HTTP_CODE);
        $result['info'] = curl_getinfo($_curl);
        if ($result['result'] === false) {
            $result['result'] = curl_error($_curl);
            $result['code'] = -curl_errno($_curl);
        }

        curl_close($_curl);
        return $result;
    }

    protected function buildResponse($curl)
    {
        if (static::CURL_RAW == true) {
            return $curl[0];
        }
        try {
            return $this->jsonToSelf($curl[0]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function jsonToSelf($json)
    {
        $obj = json_decode($json);
        $mapping = static::$result_mapping;
        foreach ($obj as $k => $v) {
            if (isset($mapping[$k])) {
                $name = $mapping[$k];
            } else {
                $name = $this->toCamel($k);
            }
            $this->$name = $v;
        }
        return $this;
    }

    protected function toCamel($string)
    {
        return preg_replace_callback(
            "/(_([a-z]))/",
            function ($match) {
                return strtoupper($match[2]);
            },
            $string
        );
    }


}