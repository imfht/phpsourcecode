<?php
namespace Yuntongxun;

use Illuminate\Support\Facades\Log;

trait YuntongxunApi
{

    /**
     * 构造curl请求header
     *
     * @return array
     */
    public function getHeader()
    {
        $sid           = $this->config['accountSid'];
        $time          = $this->time;
        $authorization = base64_encode($sid . ':' . $time);
        $header        = [
            'Accept:application/json',
            'Content-Type:application/json;charset=utf-8',
            'Authorization:' . $authorization
        ];

        return $header;
    }

    /**
     * curl get
     *
     * @param $url
     * @param $data
     *
     * @return mixed
     * @throws \Exception
     */
    public function getJson($url, $data)
    {
        try {

            $curl = curl_init($url . '&' . http_build_query($data));
            curl_setopt($curl, CURLOPT_HEADER, $this->getHeader());
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $responseText = curl_exec($curl);
            if (env('APP_DEBUG', false)) {
                Log::debug('云通讯短信GET返回内容：', [$responseText]);
            }
            curl_close($curl);

            return $responseText;
        } catch (\Exception $e) {
            Log::error('发送GET请求错误', [$e->getCode(), $e->getMessage(), $e->getTraceAsString()]);
            throw $e;
        }
    }

    /**
     * curl post
     *
     * @param $url
     * @param $data
     *
     * @return mixed
     * @throws \Exception
     */
    public function postJson($url, $data)
    {
        try {

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->getHeader());
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $responseText = curl_exec($curl);
            if (env('APP_DEBUG', false)) {
                Log::debug('云通讯短信POST返回内容：', [$responseText]);
            }
            curl_close($curl);

            return $responseText;
        } catch (\Exception $e) {
            Log::error('发送POST请求错误', [$e->getCode(), $e->getMessage(), $e->getTraceAsString()]);
            throw $e;
        }
    }

    /**
     * 构造请求链接
     *
     * @param $uri
     *
     * @return string
     */
    public function getRequestUrl($uri)
    {
        $sid   = $this->config['accountSid'];
        $time  = $this->time;
        $token = $this->config['accountToken'];
        $sign  = strtoupper(md5($sid . $token . $time));
        $url   = "{$this->config['serverUri']}:{$this->config['serverPort']}/{$this->config['softVersion']}/Accounts/{$this->config['accountSid']}/{$uri}?sig={$sign}";

        return $url;
    }

    /**
     * 以Get方式请求api
     *
     * @param $uri
     * @param $data
     *
     * @return mixed
     */
    public function responseGet($uri, $data)
    {
        $url      = $this->getRequestUrl($uri);
        $response = $this->getJson($url, $data);
        if (env('APP_DEBUG', false)) {
            Log::debug('云通讯 发送Get请求内容： ', [$uri, $data, $response]);
        }

        return json_decode($response);
    }

    /**
     * 以Post方式请求api
     *
     * @param $uri
     * @param $data
     *
     * @return mixed
     */
    public function responsePost($uri, $data)
    {
        $url      = $this->getRequestUrl($uri);
        $response = $this->postJson($url, $data);

        if (env('APP_DEBUG', false)) {
            Log::debug('云通讯 发送Get请求内容： ', [$url, $data, $response]);
        }

        return json_decode($response);
    }

}
