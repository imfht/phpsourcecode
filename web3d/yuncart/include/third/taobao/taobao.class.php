<?php

class TaoClient
{

    private $appkey;
    private $appsecret;
    public $gatewayUrl = "http://gw.api.taobao.com/router/rest";
    public $format = "json";
    protected $signMethod = "md5";
    protected $apiVersion = "2.0";
    protected $sdkVersion = "top-sdk-php-20121115";
    private $http;

    public function __construct($appkey, $appsecret)
    {
        $this->appkey = $appkey;
        $this->appsecret = $appsecret;

        require_once COMMONPATH . '/http.class.php';
        $this->http = new Http();
    }

    protected function generateSign($params)
    {
        ksort($params);

        $stringToBeSigned = $this->appsecret;
        foreach ($params as $k => $v) {
            if ("@" != substr($v, 0, 1)) {
                $stringToBeSigned .= "$k$v";
            }
        }
        unset($k, $v);
        $stringToBeSigned .= $this->appsecret;

        return strtoupper(md5($stringToBeSigned));
    }

    public function execute($request, $session = null)
    {
        //组装系统参数
        $sysParams["app_key"] = $this->appkey;
        $sysParams["v"] = $this->apiVersion;
        $sysParams["format"] = $this->format;
        $sysParams["sign_method"] = $this->signMethod;
        $sysParams["method"] = $request['method'];
        $sysParams["timestamp"] = date("Y-m-d H:i:s");
        $sysParams["partner_id"] = $this->sdkVersion;
        if (null != $session) {
            $sysParams["session"] = $session;
        }

        //获取业务参数
        $apiParams = $request['paras'];

        //签名
        $sysParams["sign"] = $this->generateSign(array_merge($apiParams, $sysParams));

        //系统参数放入GET请求串
        $requestUrl = $this->gatewayUrl . "?";
        foreach ($sysParams as $sysParamKey => $sysParamValue) {
            $requestUrl .= "$sysParamKey=" . urlencode($sysParamValue) . "&";
        }
        $requestUrl = substr($requestUrl, 0, -1);

        //发起HTTP请求

        $resp = $this->http->post($requestUrl, $apiParams);
        return $resp ? json_decode($resp, true) : array();
    }

}
