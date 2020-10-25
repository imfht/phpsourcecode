<?php
namespace Core\Platform\Alipay;

use Think\Log;

import_third('aop.AopClient');
class AliClient extends \AopClient {

    public static $aliPublic = <<<'DOC'
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkr
IvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsra
prwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUr
CmZYI/FCEa3/cNMW0QIDAQAB
-----END PUBLIC KEY-----
DOC;

    private $platform;

    function __construct($platform) {
        $this->platform = $platform;
        $this->appId = $platform['appid'];
    }


    public function  checkSignAndDecrypt($params, $isCheckSign, $isDecrypt) {
        return parent::checkSignAndDecrypt($params, null, null, $isCheckSign, $isDecrypt);
    }

    public function encryptAndSign($bizContent, $isEncrypt, $isSign) {
        return parent::encryptAndSign($bizContent, null, null, 'GBK', $isEncrypt, $isSign);
    }

    function verify($data, $sign, $rsaPublicKeyFilePath) {
        $pubKey = self::$aliPublic;
        $res = openssl_get_publickey($pubKey);
        $result = (bool) openssl_verify($data, base64_decode($sign), $res);
        openssl_free_key($res);
        return $result;
    }

    protected function sign($data) {
        $priKey = $this->platform['private_key'];
        $res = openssl_get_privatekey($priKey);
        openssl_sign($data, $sign, $res);
        openssl_free_key($res);
        $sign = base64_encode($sign);
        return $sign;
    }

    public function download($mediaId) {
        //组装系统参数
        $sysParams["app_id"] = $this->appId;
        $sysParams["version"] = $this->apiVersion;
        $sysParams["format"] = $this->format;
        $sysParams["sign_type"] = $this->signType;
        $sysParams["method"] = 'alipay.mobile.public.multimedia.download';
        $sysParams["timestamp"] = date("Y-m-d H:i:s");
        $sysParams["auth_token"] = '';
        $sysParams["alipay_sdk"] = $this->alipaySdkVersion;
        $sysParams["terminal_type"] = null;
        $sysParams["terminal_info"] = null;
        $sysParams["prod_code"] = null;

        //获取业务参数
        $media = array();
        $media['mediaId'] = $mediaId;
        $apiParams = array();
        $apiParams['biz_content'] = json_encode($media);

        //签名
        $sysParams["sign"] = $this->generateSign(array_merge($apiParams, $sysParams));

        //系统参数放入GET请求串
        $gatewayUrl = 'https://openfile.alipay.com/chat/multimedia.do';
        $requestUrl = $gatewayUrl . "?";
        foreach ($sysParams as $sysParamKey => $sysParamValue) {
            $requestUrl .= "$sysParamKey=" . urlencode($sysParamValue) . "&";
        }
        $requestUrl = substr($requestUrl, 0, -1);

        //发起HTTP请求
        try {
            $resp = $this->curl($requestUrl, $apiParams);
        } catch (Exception $e) {
            $this->logCommunicationError($sysParams["method"], $requestUrl, "HTTP_ERROR_" . $e->getCode(), $e->getMessage());
            return false;
        }

        return $resp;
    }
}
