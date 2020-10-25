<?php
//require_once("SnsNetwork.php");
/**
 * CosUtil，云对象存储辅助类；继承 SnsNetwork类，并新增方法 makeUploadRequest, makeCosSig
 *
 * @version 1.0.1
 * @copyright © 2012, Tencent Corporation. All rights reserved.
 *
 */

class CosUtil
{
    static public function makeRequest($url, $params, $cookie, $method='post', $protocol='http')
    {
        $query_string = self::makeQueryString($params);

        echo "query_string : ".$query_string;
        $cookie_string = self::makeCookieString($cookie);

        $ch = curl_init();

        if (strcasecmp('get', $method) == 0)
        {
            curl_setopt($ch, CURLOPT_URL, "$url?$query_string");
        }
        else if(strcasecmp('post', $method) == 0)
        {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
        }
        else
        {
            curl_setopt($ch, CURLOPT_URL, "$url?$query_string");
        }

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

        // disable 100-continue
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

        if (!empty($cookie_string))
        {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie_string);
        }

        if ('https' == $protocol)
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $ret = curl_exec($ch);
        $err = curl_error($ch);

        if (false === $ret || !empty($err))
        {
            $errno = curl_errno($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            return array(
                'result' => false,
                'errno' => $errno,
                'msg' => $err,
                'info' => $info,
            );
        }

        curl_close($ch);

        return array(
            'result' => true,
            'msg' => $ret,
        );

    }

    static protected function makeQueryString($params)
    {
        if (is_string($params))
            return $params;

        $query_string = array();
        foreach ($params as $key => $value)
        {
            array_push($query_string, rawurlencode($key) . '=' . rawurlencode($value));
        }
        $query_string = join('&', $query_string);
        return $query_string;
    }

    static protected function makeCookieString($params)
    {
        if (is_string($params))
            return $params;

        $cookie_string = array();
        foreach ($params as $key => $value)
        {
            array_push($cookie_string, $key . '=' . $value);
        }
        $cookie_string = join('; ', $cookie_string);
        return $cookie_string;
    }

    static public function makeUploadRequest($url, $params, $cookie,$request_body ,$protocol='http')
    {
        $method='PUT';
        $query_string = self::makeQueryString($params);
        echo "query_string put : ".$query_string;  
        $cookie_string = self::makeCookieString($cookie);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "$url?$query_string");

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

        // disable 100-continue
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        if (!empty($cookie_string))
        {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie_string);
        }
        if ('https' == $protocol)
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);

        $ret = curl_exec($ch);
        $err = curl_error($ch);
        if (false === $ret || !empty($err))
        {
            $errno = curl_errno($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            return array(
                'result' => false,
                'errno' => $errno,
                'msg' => $err,
                'info' => $info,
            );
        }

        curl_close($ch);

        return array(
            'result' => true,
            'msg' => $ret,
        );
    }

    /**
     * 生成 Cos签名，和sns生成签名的方式有差异
     *
     * @param string 	$method 请求方法 "get" or "post"
     * @param string 	$url_path 
     * @param array 	$params 表单参数
     * @param string 	$secret 密钥
     */
    static public function makeCosSig($method, $url_path, $params, $secret)
    {
        $mk = self::makeSource($method, $url_path, $params);
        $my_sign = hash_hmac("sha1", $mk, strtr($secret, '-_', '+/'), true);
        $my_sign = base64_encode($my_sign);

        return $my_sign;
    }

    static private function makeSource($method, $url_path, $params)
    {
        $strs = rawurlencode($url_path .'&');

        ksort($params);
        $query_string = array();
        foreach ($params as $key => $val ) 
        { 
            array_push($query_string, $key . '=' . $val);
        }   
        $query_string = join('&', $query_string);

        return $strs . rawurlencode($query_string);
    }
    static public function getDownloadSign($params,$secret)
    {
        ksort($params);
        $query_string = array();
        foreach ($params as $key => $val ) 
        { 
            array_push($query_string, $key . '=' . $val);
        }   
        $query_string = join('&', $query_string);
        $source = rawurlencode($query_string);
        $my_sign = hash_hmac("sha1", $source, strtr($secret, '-_', '+/'), true);
        $my_sign = base64_encode($my_sign);
        return 	$my_sign;
    }
}

// end of script
