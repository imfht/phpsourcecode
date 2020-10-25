<?php

/*
 *  @author myf
 *  @date 2014-11-15 
 *  @Description myfmvc http请求类
 *  @web http://www.minyifei.cn
 */
namespace Myf\Mvc;
use Myf\Mvc\Log;

class Http {
    
    /**
     * GET请求
     * @param String $url 请求地址
     * @param Array $params 参数
     * @param Array $headers header扩展信息
     */
    public static function get($url,$params=array(),$headers=array()){
        return self::request($url, $params, "GET", $headers, FALSE);
    }
    
    /**
     * POST请求
     * @param String $url 请求地址
     * @param Array $params 参数
     * @param Array $headers header扩展信息
     * @param Array $multi 文件
     */
    public static function post($url,$params=array(),$headers=array(),$multi=false){
       return self::request($url, $params, "POST", $headers, $multi);
    }

    /**
     * 发起一个HTTP/HTTPS的请求
     * @param $url 接口的URL 
     * @param $params 接口参数   array('content'=>'test', 'format'=>'json');
     * @param $method 请求类型，GET|POST|JSON|DELETE
     * @param $headers 扩展的包头信息
     * @param $multi 图片信息
     * @return string
     */
    public static function request($url, $params = array(), $method = 'GET', $headers = array(), $multi = false) {
        if (!function_exists('curl_init')) {
            exit('Need to open the curl extension');
        }
        $showLog = C("OPEN_HTTP_LOG");
        try{
            $httpStartTime = getMillisecond();
            $method = strtoupper($method);
            $ci = curl_init();
            curl_setopt($ci, CURLOPT_USERAGENT, 'myfmvc php client');
            curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ci, CURLOPT_TIMEOUT, 10);
            curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ci, CURLOPT_HEADER, false);
            switch ($method) {
                case 'POST':
                    curl_setopt($ci, CURLOPT_POST, TRUE);
                    if (!empty($params)) {
                        if ($multi) {
                            foreach ($multi as $key => $file) {
                                $params[$key] = '@' . $file;
                            }
                            curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
                            $headers[] = 'Expect: ';
                        } else {
                            curl_setopt($ci, CURLOPT_POSTFIELDS, http_build_query($params));
                        }
                    }
                    break;
                case 'DELETE':
                case 'GET':
                    $method == 'DELETE' && curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    if (!empty($params)) {
                        $url = $url . (strpos($url, '?') ? '&' : '?')
                                . (is_array($params) ? http_build_query($params) : $params);
                    }
                    break;
                case 'JSON':
                    if (!empty($params)) {
                        $data = json_encode($params);
                        curl_setopt($ci, CURLOPT_POSTFIELDS, $data);
                        curl_setopt($ci, CURLOPT_HTTPHEADER, array(
                            'Content-Type: application/json',
                            'Content-Length: ' . strlen($data)));
                        curl_setopt($ci, CURLOPT_CUSTOMREQUEST, "POST");
                    }
                    break;
            }
            curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
            curl_setopt($ci, CURLOPT_URL, $url);
            if ($headers) {
                curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
            }
            $response = curl_exec($ci);
            curl_close($ci);
            $httpEndTime = getMillisecond();
            if($showLog){
                Log::write(sprintf("HTTP COSTTIME=【%s】ms, URL=【%s】,RESPONSE=【%s】",($httpEndTime-$httpStartTime),$url,$response));
            }
            return $response;   
        } catch (Exception $ex) {
            if($showLog){
                Log::write(sprintf("HTTP URL=【%s】,ERRORS=【%s】",$url,$ex->getMessage()));
            }
        }
    }

}
