<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace wechat; 
class Curl {

    // GET
    public static function get($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查 
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97'); // 模拟用户使用的浏览器 
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转 
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer 
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回 
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    // POST
    public static function post($url, $postData) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_REFERER, 'https://mp.weixin.qq.com/');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查 
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97'); // 模拟用户使用的浏览器 
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转 
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer 
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("content-type: application/x-www-form-urlencoded;charset=UTF-8"));
        // post数据
        curl_setopt($curl, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    // PUT
    public static function put($url, $putData) {
        $ch = curl_init(); //初始化CURL句柄 
        curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); //设置请求方式
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-HTTP-Method-Override: PUT")); //设置HTTP头信息
        curl_setopt($ch, CURLOPT_POSTFIELDS, $putData); //设置提交的字符串
        $document = curl_exec($ch); //执行预定义的CURL 
        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
            echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'];
        } else {
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
        return $document;
    }

}
