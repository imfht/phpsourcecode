<?php

/**
 * http远程请求工具
 * Date: 16-10-8
 * Time: 下午8:53
 * author :李华 yehong0000@163.com
 */
namespace tool;

use log\Log;

class Http
{
    protected static $timeOut = 60;

    /**
     * @param string $url
     * @param array string $params
     * @param string $header
     */
    static public function post($url, $params, &$header = [])
    {
        $data = array();
        if (is_array($params)) {
            foreach ($params as $k => $v) {
                $data[] = $k . '=' . urlencode($v);
            }
            $data = join('&', $data);
        } else {
            $data = $params;
        }
        return self::execute($url, $data, 1, $header);
    }

    /**
     * get请求
     *
     * @param string $url
     * @param array $params
     * @param string $header
     */
    static public function get($url, $params, &$header = [])
    {
        $index = strpos($url, '?');
        if ($index !== false && $params) {
            $url .= '&' . http_build_query($params, '&');
        } elseif($params){
            $url .= '?' . http_build_query($params, '&');
        }
        return self::execute($url, null, 2, $header);
    }

    /**
     * 文件发送
     *
     * @param $url
     * @param $path
     * @param $name
     * @param $header
     */
    static public function file($url, $path, $name, &$header = [])
    {
        $data = array(
            'file'     => '@' . $path,
            'filename' => $name,
        );
        return self::execute($url, $data, 1, $header);
    }

    /**
     * put 发送适合大部分微信接口请求
     *
     * @param $url
     * @param string $params
     * @param $header
     */
    static public function put($url, $params, &$header = [])
    {
        return self::execute($url, $params, 3, $header);
    }

    /**
     * 设置超时时间
     *
     * @param $second
     */
    static public function setTimeOut($second)
    {
        self::$timeOut = $second;
    }

    /**
     * 执行
     *
     * @param $url
     * @param string $data
     * @param int $type 1 post | 2 get |3 put
     * @param $header
     *
     * @return string
     * @throws \Exception
     */
    static private function execute($url, $data, $type = 1, &$header)
    {
        $cu = curl_init();#开始curl会话
        if (!function_exists("curl_init") &&
            !function_exists("curl_setopt") &&
            !function_exists("curl_exec") &&
            !function_exists("curl_close")
        )
            throw new \Exception('curl模块错误！', -8201);

        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($cu, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($cu, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($cu, CURLOPT_SSL_VERIFYPEER, FALSE);    // https请求 不验证证书和hosts
            curl_setopt($cu, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if ($type == 1) {
            curl_setopt($cu, CURLOPT_POST, true);
            curl_setopt($cu, CURLOPT_POSTFIELDS, $data);
        } elseif ($type == 3) {
            curl_setopt($cu, CURLOPT_HTTPHEADER, array("X-HTTP-Method-Override: put"));//设置HTTP头信息
            curl_setopt($cu, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($cu, CURLOPT_URL, $url);
        curl_setopt($cu, CURLOPT_TIMEOUT, self::$timeOut);
        curl_setopt($cu, CURLOPT_HEADER, true);//http头
        curl_setopt($cu, CURLOPT_RETURNTRANSFER, TRUE);#内容做为变量存储

        $tmp = curl_exec($cu);
        if (!curl_error($cu) && curl_getinfo($cu, CURLINFO_HTTP_CODE) == '200') {
            $headerSize = curl_getinfo($cu, CURLINFO_HEADER_SIZE);#取得头长度
            $header = substr($tmp, 0, $headerSize);#获得头内容
            $body = substr($tmp, $headerSize);#获得文件内容
            if (curl_getinfo($cu, CURLINFO_SIZE_DOWNLOAD) <= 100) {//如果内容长度小于100进行错误检查在微信文件调用时有用
                $msg = json_decode($body, true);
                if (is_numeric($msg['errcode'])) {
                    throw new \Exception($msg['errmsg'], $msg['errcode']);
                }
            }
            curl_close($cu);
            return $body;
        } else {
            $err = curl_error($cu);
            curl_close($cu);
            throw new \Exception($err, -8200);
        }
    }
}