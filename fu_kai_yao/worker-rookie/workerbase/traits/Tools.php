<?php
namespace workerbase\traits;

/**
 * @author fukaiyao
 * Trait Tools
 * @package workerbase\traits
 */
trait Tools{
    /**
	 * 获取13位毫秒时间戳
	 * @return string
	 */
	public function  getMicrotime() {
		list($t1, $t2) = explode(' ', microtime());
		return sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);;
	}

    /**
     * 将二维数组转化为对象数组
     * @param array $arr
     * @return array
     */
    public function arrayToObjects(array $arr)
    {
        $ret = [];
        foreach ($arr as $item) {
            $ret[] = (object) $item;
        }
        return $ret;
    }

    /**
     * 	作用：array转xml
     */
    public function arrayToXml($arr, $root = 'root')
    {
        $xml = "<{$root}>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val))
            {
                $xml.="<".$key.">".$val."</".$key.">";

            }
            else if(is_array($val)) {
                $xml .= $this->arrayToXml($val, $key);
            }
            else {
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</{$root}>";
        return $xml;
    }

    /**
     * 	作用：将xml转为array
     */
    public function xmlToArray($xml)
    {
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    /**
     * 生成签名
     * @param array $paramArray   加密参数数组
     * @param string $secret   秘钥
     * @return string
     */
    public function createOpenSign($paramArray, $secret){
        if (!is_array($paramArray)) return false;
        ksort($paramArray, SORT_STRING);
        $str = http_build_query($paramArray);//会自动urlencode
        $sign = strtoupper(md5(md5($str).$secret));
        return $sign;
    }

    /**
     * Rsa加密
     * @param string $publicKey - 公钥路径或者公钥字符串
     * @param string $content   - 待加密内容
     * @return string
     */
    public function rsaEncrypt($publicKey, $content)
    {
        if (empty($publicKey)) {
            echo "<br/>rsa公钥不能为空, key=".$publicKey."<br/>";
            return false;
        }

        if (is_file($publicKey)) {
            //读取公钥文件
            $pubKey = file_get_contents($publicKey);
            //转换为openssl格式密钥
            $res = openssl_get_publickey($pubKey);
        } else {
            //读取字符串
            $res = "-----BEGIN PUBLIC KEY-----\n" .
                wordwrap($publicKey, 64, "\n", true) .
                "\n-----END PUBLIC KEY-----";
        }

        ($res) or die('RSA公钥错误。请检查公钥文件格式是否正确');
        $crypto = '';
        foreach (str_split($content, 117) as $chunk) {
            if (!openssl_public_encrypt($chunk, $encryptData, $res)) {
                echo "<br/>rsa encrypt error, " . openssl_error_string() . "<br/>";
                return '';
            }
            $crypto .= $encryptData;
        }
        return base64_encode($crypto);
    }

    /**
     * rsa解密
     * @param string $privateKey    - 私钥路径或者私钥字符串
     * @param string $encryptData   - 密文
     * @return bool|string
     */
    public function rsaDecrypt($privateKey, $encryptData)
    {
        if (empty($privateKey)) {
            return false;
        }

        if (is_file($privateKey)) {
            //读取私钥文件
            $priKey = file_get_contents($privateKey);
            //转换为openssl格式密钥
            $res = openssl_get_privatekey($priKey);
        } else {
            //读字符串
            $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
                wordwrap($privateKey, 64, "\n", true) .
                "\n-----END RSA PRIVATE KEY-----";
        }

        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');
        $crypto = '';
        foreach (str_split(base64_decode($encryptData), 256) as $chunk) {
            if (!openssl_private_decrypt($chunk, $decryptData, $res)) {
                echo "<br/>rsa decrypt error, " . openssl_error_string() . "<br/>";
                return false;
            }
            $crypto .= $decryptData;
        }
        return $crypto;
    }

    /**
     * 生成请求串号
     * @return int
     */
    public function getRequestId()
    {
        $us = strstr(microtime(), ' ', true);
        return intval(strval($us * 1000 * 1000) . rand(100, 999));
    }

}