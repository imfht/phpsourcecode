<?php
/**
 * Created by china_wangyu@aliyun.com. Date: 2018/11/26 Time: 17:19
 */

namespace WeChat\Extend;

/**
 * Class Request 请求类
 * @package wechat\lib
 */
class Request
{
    /**
     * 支持请求类型
     * @var array $methods
     */
    private static $methods = ['get','post'];

    /**
     * 发送 header 请求
     * @param string $url 请求链接
     * @param array $params 请求参数
     */
    public static function header(string $url,array $params = []):void
    {
        if (!empty($params)) $url .= static::ToUrlParams($params);
        header('Location: '  . $url);
        exit();
    }

    /**
     * 发送 <script>window.top.location.href</script> 请求
     * @param string $url
     * @param array $params
     */
    public static function jump(string $url,array $params = [])
    {
        if (!empty($params)) $url .= static::ToUrlParams($params);
        exit( '<script>window.top.location.href='.$url.'</script>');
    }

    /**
     * 发送curl请求
     * @param string $method 【类型 : get | post】
     * @param string $url   请求链接
     * @param array $params  请求参数
     * @return array
     */
    public static function request(string $method,string $url, $params = []):array
    {
        $method = strtolower($method);
        $isHttp = stristr($url,'https') ?  true : false;
        if (!in_array($method,static::$methods)) Json::error('请求类型错误~');
        if ($method === 'get' and !empty($params)) $url .= static::ToUrlParams($params);
        return static::curl_request($url,$isHttp,$method,$params);
    }

    /**
     * [curl_request 发送http请求]
     * @param  [url]    $url                                                      [请求地址]
     * @param  boolean  $https                                                    [是否使用HTTPS]
     * @param  string   $method                                                   [请求方式：GET / POST]
     * @param  [array]  $data                                                     [post 数据]
     * @return [result] [成功返回对方返回的结果，是非返回false]
     */
    public static function curl_request($url, $https = false, $method = 'get', $data = null)
    {
        /****************      初始化curl     ******************/
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //结果为字符串且输出到屏幕上
        /****************     发送 https请求     ******************/
        if ($https === true) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        /********      发送 POST 请求  类型为：application/x-www-form-urlencoded    **********/
        if ($method == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
            curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
            // 所需传的数组用 http_build_query() 函数处理一下，就可以传递二维数组了
            if (is_array($data) and count($data) > 0) $data = http_build_query($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            curl_setopt($ch, CURLOPT_TIMEOUT, 500);
        }
        /****************      发送请求    ******************/
        curl_setopt($ch, CURLOPT_URL, $url);
        $result     = curl_exec($ch);
        $url_status = curl_getinfo($ch);
        /****************      关闭连接 并 返回数据    ******************/
        curl_close($ch);

        if (intval($url_status["http_code"]) == 200){
            if (json_decode($result,true) != false){
                return json_decode($result,true);
            }
            return $result;
        }
        return false;
    }



    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     *
     * @return 返回已经拼接好的字符串
     */
    public static function ToUrlParams($urlObj)
    {
        $buff = "?";
        foreach ($urlObj as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }
}