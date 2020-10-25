<?php

class Common_util_pub
{

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function trimString($value)
    {
        $ret = null;
        
        if (null != $value) {
            $ret = $value;
            if (strlen($ret) == 0)
                $ret = null;
        }
        
        return $ret;
    }

    public function createNoncestr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        
        for ($i = 0; $i < $length; $i ++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        
        return $str;
    }

    public function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        
        foreach ($paraMap as $k => $v) {
            if ($urlencode)
                $v = urlencode($v);
                
                // $buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        
        $reqPar;
        if (strlen($buff) > 0)
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        
        return $reqPar;
    }

    public function getSign($Obj)
    {
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }
        
        ksort($Parameters);
        
        $String = $this->formatBizQueryParaMap($Parameters, false);
        $String = $String . "&key=" . $this->config['KEY'];
        $String = md5($String);
        
        $result_ = strtoupper($String);
        
        return $result_;
    }

    public function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val))
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        
        $xml .= "</xml>";
        
        return $xml;
    }

    public function xmlToArray($xml)
    {
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    public function postXmlCurl($xml, $url, $second = 30)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOP_TIMEOUT, $second);
        
        // 这里设置代理，如果有的话
        // curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        // curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        
        $data = curl_exec($ch);
        
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error" . "<br>";
            echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
            curl_close($ch);
            return false;
        }
    }

    public function postXmlSSLCurl($xml, $url, $second = 30)
    {
        $ch = curl_init();
        // 超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        
        // 这里设置代理，如果有的话
        // curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        // curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $this->config['SSLCERT_PATH']);
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, $this->config['SSLKEY_PATH']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        
        $data = curl_exec($ch);
        
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error" . "<br>";
            echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
            curl_close($ch);
            return false;
        }
    }

    public function printErr($wording = '', $err = '')
    {
        print_r('<pre>');
        echo $wording . "</br>";
        var_dump($err);
        print_r('</pre>');
    }
}

class Wxpay_client_pub extends Common_util_pub
{

    protected $parameters;

    protected $url;

    protected $curl_timeout;

    public $response;

    public $result;

    public function setParameter($parameter, $parameterValue)
    {
        $this->parameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function createXml()
    {
        $this->parameters["appid"] = $this->config['APPID']; // 公众账号ID
        $this->parameters["mch_id"] = $this->config['MCHID']; // 商户号
        $this->parameters["nonce_str"] = $this->createNoncestr(); // 随机字符串
        $this->parameters["sign"] = $this->getSign($this->parameters); // 签名
        
        return $this->arrayToXml($this->parameters);
    }

    public function postXml()
    {
        $xml = $this->createXml();
        $this->response = $this->postXmlCurl($xml, $this->url, $this->curl_timeout);
        return $this->response;
    }

    public function postXmlSSL()
    {
        $xml = $this->createXml();
        $this->response = $this->postXmlSSLCurl($xml, $this->url, $this->curl_timeout);
        return $this->response;
    }

    public function getResult()
    {
        $this->postXml();
        $this->result = $this->xmlToArray($this->response);
        return $this->result;
    }
}

class UnifiedOrder_pub extends Wxpay_client_pub
{

    public function __construct($config)
    {
        $this->config = $config;
        $this->url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $this->curl_timeout = $this->config['CURL_TIMEOUT'];
    }

    public function createXml()
    {
        try {
            if ($this->parameters["out_trade_no"] == null) {
                throw new SDKRuntimeException("缺少统一支付接口必填参数out_trade_no！" . "<br>");
            } elseif ($this->parameters["body"] == null) {
                throw new SDKRuntimeException("缺少统一支付接口必填参数body！" . "<br>");
            } elseif ($this->parameters["total_fee"] == null) {
                throw new SDKRuntimeException("缺少统一支付接口必填参数total_fee！" . "<br>");
            } elseif ($this->parameters["notify_url"] == null) {
                throw new SDKRuntimeException("缺少统一支付接口必填参数notify_url！" . "<br>");
            } elseif ($this->parameters["trade_type"] == null) {
                throw new SDKRuntimeException("缺少统一支付接口必填参数trade_type！" . "<br>");
            } elseif ($this->parameters["trade_type"] == "JSAPI" && $this->parameters["openid"] == NULL) {
                throw new SDKRuntimeException("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！" . "<br>");
            }
            
            $this->parameters["appid"] = $this->config['APPID']; // 公众账号ID
            $this->parameters["mch_id"] = $this->config['MCHID']; // 商户号
            $this->parameters["spbill_create_ip"] = $_SERVER['REMOTE_ADDR']; // 终端ip
            $this->parameters["nonce_str"] = $this->createNoncestr(); // 随机字符串
            $this->parameters["sign"] = $this->getSign($this->parameters); // 签名
            
            return $this->arrayToXml($this->parameters);
        } catch (SDKRuntimeException $e) {
            die($e->errorMessage());
        }
    }

    public function getPrepayId()
    {
        $this->postXml();
        $this->result = $this->xmlToArray($this->response);
        $prepay_id = $this->result["prepay_id"];
        return $prepay_id;
    }
}

class OrderQuery_pub extends Wxpay_client_pub
{

    public function __construct($config)
    {
        $this->config = $config;
        $this->url = "https://api.mch.weixin.qq.com/pay/orderquery";
        $this->curl_timeout = $this->config['CURL_TIMEOUT'];
    }

    public function createXml()
    {
        try {
            // 检测必填参数
            if ($this->parameters["out_trade_no"] == null && $this->parameters["transaction_id"] == null) {
                throw new SDKRuntimeException("订单查询接口中，out_trade_no、transaction_id至少填一个！" . "<br>");
            }
            
            $this->parameters["appid"] = $this->config['APPID']; // 公众账号ID
            $this->parameters["mch_id"] = $this->config['MCHID']; // 商户号
            $this->parameters["nonce_str"] = $this->createNoncestr(); // 随机字符串
            $this->parameters["sign"] = $this->getSign($this->parameters); // 签名
            
            return $this->arrayToXml($this->parameters);
        } catch (SDKRuntimeException $e) {
            die($e->errorMessage());
        }
    }
}

class Refund_pub extends Wxpay_client_pub
{

    public function __construct($config)
    {
        $this->config = $config;
        $this->url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
        $this->curl_timeout = $this->config['CURL_TIMEOUT'];
    }

    public function createXml()
    {
        try {
            if ($this->parameters["out_trade_no"] == null && $this->parameters["transaction_id"] == null) {
                throw new SDKRuntimeException("退款申请接口中，out_trade_no、transaction_id至少填一个！" . "<br>");
            } elseif ($this->parameters["out_refund_no"] == null) {
                throw new SDKRuntimeException("退款申请接口中，缺少必填参数out_refund_no！" . "<br>");
            } elseif ($this->parameters["total_fee"] == null) {
                throw new SDKRuntimeException("退款申请接口中，缺少必填参数total_fee！" . "<br>");
            } elseif ($this->parameters["refund_fee"] == null) {
                throw new SDKRuntimeException("退款申请接口中，缺少必填参数refund_fee！" . "<br>");
            } elseif ($this->parameters["op_user_id"] == null) {
                throw new SDKRuntimeException("退款申请接口中，缺少必填参数op_user_id！" . "<br>");
            }
            
            $this->parameters["appid"] = $this->config['APPID']; // 公众账号ID
            $this->parameters["mch_id"] = $this->config['MCHID']; // 商户号
            $this->parameters["nonce_str"] = $this->createNoncestr(); // 随机字符串
            $this->parameters["sign"] = $this->getSign($this->parameters); // 签名
            
            return $this->arrayToXml($this->parameters);
        } catch (SDKRuntimeException $e) {
            die($e->errorMessage());
        }
    }

    public function getResult()
    {
        $this->postXmlSSL();
        $this->result = $this->xmlToArray($this->response);
        return $this->result;
    }
}

class RefundQuery_pub extends Wxpay_client_pub
{

    public function __construct($config)
    {
        $this->config = $config;
        $this->url = "https://api.mch.weixin.qq.com/pay/refundquery";
        // 设置curl超时时间
        $this->curl_timeout = $this->config['CURL_TIMEOUT'];
    }

    public function createXml()
    {
        try {
            if ($this->parameters["out_refund_no"] == null && $this->parameters["out_trade_no"] == null && $this->parameters["transaction_id"] == null && $this->parameters["refund_id "] == null) {
                throw new SDKRuntimeException("退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个！" . "<br>");
            }
            
            $this->parameters["appid"] = $this->config['APPID']; // 公众账号ID
            $this->parameters["mch_id"] = $this->config['MCHID']; // 商户号
            $this->parameters["nonce_str"] = $this->createNoncestr(); // 随机字符串
            $this->parameters["sign"] = $this->getSign($this->parameters); // 签名
            
            return $this->arrayToXml($this->parameters);
        } catch (SDKRuntimeException $e) {
            die($e->errorMessage());
        }
    }

    public function getResult()
    {
        $this->postXmlSSL();
        $this->result = $this->xmlToArray($this->response);
        return $this->result;
    }
}

class DownloadBill_pub extends Wxpay_client_pub
{

    public function __construct($config)
    {
        $this->config = $config;
        $this->url = "https://api.mch.weixin.qq.com/pay/downloadbill";
        $this->curl_timeout = $this->config['CURL_TIMEOUT'];
    }

    public function createXml()
    {
        try {
            if ($this->parameters["bill_date"] == null) {
                throw new SDKRuntimeException("对账单接口中，缺少必填参数bill_date！" . "<br>");
            }
            
            $this->parameters["appid"] = $this->config['APPID']; // 公众账号ID
            $this->parameters["mch_id"] = $this->config['MCHID']; // 商户号
            $this->parameters["nonce_str"] = $this->createNoncestr(); // 随机字符串
            $this->parameters["sign"] = $this->getSign($this->parameters); // 签名
            
            return $this->arrayToXml($this->parameters);
        } catch (SDKRuntimeException $e) {
            die($e->errorMessage());
        }
    }

    public function getResult()
    {
        $this->postXml();
        $this->result = $this->xmlToArray($this->result_xml);
        return $this->result;
    }
}

class ShortUrl_pub extends Wxpay_client_pub
{

    public function __construct($config)
    {
        $this->config = $config;
        $this->url = "https://api.mch.weixin.qq.com/tools/shorturl";
        $this->curl_timeout = $this->config['CURL_TIMEOUT'];
    }

    public function createXml()
    {
        try {
            if ($this->parameters["long_url"] == null) {
                throw new SDKRuntimeException("短链接转换接口中，缺少必填参数long_url！" . "<br>");
            }
            
            $this->parameters["appid"] = $this->config['APPID']; // 公众账号ID
            $this->parameters["mch_id"] = $this->config['MCHID']; // 商户号
            $this->parameters["nonce_str"] = $this->createNoncestr(); // 随机字符串
            $this->parameters["sign"] = $this->getSign($this->parameters); // 签名
            
            return $this->arrayToXml($this->parameters);
        } catch (SDKRuntimeException $e) {
            die($e->errorMessage());
        }
    }

    public function getShortUrl()
    {
        $this->postXml();
        $prepay_id = $this->result["short_url"];
        return $prepay_id;
    }
}

class Wxpay_server_pub extends Common_util_pub
{

    public $data;

    protected $returnParameters;

    public function saveData($xml)
    {
        $this->data = $this->xmlToArray($xml);
    }

    public function checkSign()
    {
        $tmpData = $this->data;
        unset($tmpData['sign']);
        $sign = $this->getSign($tmpData); // 本地签名
        
        if ($this->data['sign'] == $sign)
            return TRUE;
        
        return FALSE;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setReturnParameter($parameter, $parameterValue)
    {
        $this->returnParameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
    }

    public function createXml()
    {
        return $this->arrayToXml($this->returnParameters);
    }

    public function returnXml()
    {
        $returnXml = $this->createXml();
        return $returnXml;
    }
}

class Notify_pub extends Wxpay_server_pub
{
}

class NativeCall_pub extends Wxpay_server_pub
{

    public function createXml()
    {
        if ($this->returnParameters["return_code"] == "SUCCESS") {
            $this->returnParameters["appid"] = $this->config['APPID']; // 公众账号ID
            $this->returnParameters["mch_id"] = $this->config['MCHID']; // 商户号
            $this->returnParameters["nonce_str"] = $this->createNoncestr(); // 随机字符串
            $this->returnParameters["sign"] = $this->getSign($this->returnParameters); // 签名
        }
        
        return $this->arrayToXml($this->returnParameters);
    }

    public function getProductId()
    {
        $product_id = $this->data["product_id"];
        return $product_id;
    }
}

class NativeLink_pub extends Common_util_pub
{

    protected $parameters;

    protected $url;

    public function setParameter($parameter, $parameterValue)
    {
        $this->parameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
    }

    public function createLink()
    {
        try {
            if ($this->parameters["product_id"] == null) {
                throw new SDKRuntimeException("缺少Native支付二维码链接必填参数product_id！" . "<br>");
            }
            
            $time_stamp = time();
            
            $this->parameters["appid"] = $this->config['APPID']; // 公众账号ID
            $this->parameters["mch_id"] = $this->config['MCHID']; // 商户号
            $this->parameters["time_stamp"] = "{$time_stamp}"; // 时间戳
            $this->parameters["nonce_str"] = $this->createNoncestr(); // 随机字符串
            $this->parameters["sign"] = $this->getSign($this->parameters); // 签名
            
            $bizString = $this->formatBizQueryParaMap($this->parameters, false);
            $this->url = "weixin://wxpay/bizpayurl?" . $bizString;
        } catch (SDKRuntimeException $e) {
            die($e->errorMessage());
        }
    }

    public function getUrl()
    {
        $this->createLink();
        return $this->url;
    }
}

class JsApi_pub extends Common_util_pub
{

    protected $code;

    protected $openid;

    protected $parameters;

    protected $prepay_id;

    protected $curl_timeout;

    public function __construct($config)
    {
        $this->config = $config;
        $this->curl_timeout = $this->config['CURL_TIMEOUT'];
    }

    public function createOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"] = $this->config['APPID'];
        $urlObj["redirect_uri"] = "{$redirectUrl}";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "STATE" . "#wechat_redirect";
        $bizString = $this->formatBizQueryParaMap($urlObj, false);
        
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
    }

    public function createOauthUrlForOpenid()
    {
        $urlObj["appid"] = $this->config['APPID'];
        $urlObj["secret"] = $this->config['APPSECRET'];
        $urlObj["code"] = $this->code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->formatBizQueryParaMap($urlObj, false);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?" . $bizString;
    }

    public function getOpenid()
    {
        $url = $this->createOauthUrlForOpenid();
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOP_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        $res = curl_exec($ch);
        curl_close($ch);
        
        $data = json_decode($res, true);
        $this->openid = $data['openid'];
        
        return $this->openid;
    }

    public function setPrepayId($prepayId)
    {
        $this->prepay_id = $prepayId;
    }

    public function setCode($code_)
    {
        $this->code = $code_;
    }

    public function getParameters()
    {
        $timeStamp = time();
        
        $jsApiObj["appId"] = $this->config['APPID'];
        $jsApiObj["timeStamp"] = "{$timeStamp}";
        $jsApiObj["nonceStr"] = $this->createNoncestr();
        $jsApiObj["package"] = "prepay_id=$this->prepay_id";
        $jsApiObj["signType"] = "MD5";
        $jsApiObj["paySign"] = $this->getSign($jsApiObj);
        
        $this->parameters = json_encode($jsApiObj);
        
        return $this->parameters;
    }
}

class RedPack_pub extends Common_util_pub
{

    private $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';

    private $parameters;

    private $curl_timeout;

    public $response;

    public $result;

    public function setParameter($parameter, $parameterValue)
    {
        $this->parameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function createXml()
    {
        $this->parameters["wxappid"] = $this->config['APPID'];
        $this->parameters["mch_id"] = $this->config['MCHID'];
        $this->parameters["nonce_str"] = $this->createNoncestr();
        $this->parameters["sign"] = $this->getSign($this->parameters);
        
        return $this->arrayToXml($this->parameters);
    }

    public function postXmlSSL()
    {
        $xml = $this->createXml();
        $this->response = $this->_postXmlSSLCurl($xml, $this->url, $this->curl_timeout);
        
        return $this->response;
    }

    private function _postXmlSSLCurl($xml, $url, $second = 30)
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $this->config['SSLCERT_PATH']);
        
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, $this->config['SSLKEY_PATH']);
        
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_CAINFO, $this->config['CAINFO_PATH']);
        
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        
        $data = curl_exec($ch);
        
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            log_debug("curl出错，错误码:{$error}");
            curl_close($ch);
            return false;
        }
    }
}

class Transfer_pub extends Common_util_pub
{

    private $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';

    private $parameters;

    private $curl_timeout;

    public $response;

    public $result;

    public function setParameter($parameter, $parameterValue)
    {
        $this->parameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function createXml()
    {
        $this->parameters["mch_appid"] = $this->config['APPID'];
        $this->parameters["mchid"] = $this->config['MCHID'];
        $this->parameters["nonce_str"] = $this->createNoncestr();
        $this->parameters["sign"] = $this->getSign($this->parameters);
        
        return $this->arrayToXml($this->parameters);
    }

    public function postXmlSSL()
    {
        $xml = $this->createXml();
        $this->response = $this->_postXmlSSLCurl($xml, $this->url, $this->curl_timeout);
        
        return $this->response;
    }

    private function _postXmlSSLCurl($xml, $url, $second = 30)
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $this->config['SSLCERT_PATH']);
        
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, $this->config['SSLKEY_PATH']);
        
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_CAINFO, $this->config['CAINFO_PATH']);
        
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        
        $data = curl_exec($ch);
        
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            log_debug("curl出错，错误码:{$error}");
            curl_close($ch);
            return false;
        }
    }
}
?>
