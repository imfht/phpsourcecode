<?php

/**
 * curl简单get/post请求处理
 */
class SimpleCurl
{
    private $now = null;

    //构造方法
    public function __construct()
    {
        $this->now = date('Y-m-d H:i:s');
    }

    /**
     * get请求
     * @param  string  $url         请求的url,url中不可以带有参数,参数请在$params中设置
     * @param  array   $params      请求参数
     * @param  boolean $is_array    是否将结果转化为array形式返回
     * @param  array   $header      设置的header
     * @param  array   $basic_auth  array('username'=>'', 'passwork'=>'')
     * @return array/string         如果设置$is_array为true则返回array,false则直接返回请求结果
     */
    public function get($url, $params = null, $is_array = false, $headers = null, $basic_auth = null)
    {
        //初始化
        $curl = curl_init();
        if (isset($params)) {
            if (is_array($params)) {
                $url .= '?';
                $kvs = array();
                foreach ($params as $k => $v) {
                    $kv = $k . '=' . $v;
                    $kvs[] = $kv;
                }
                $url .= implode('&', $kvs);
            } else {
                //设置post数据
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
            }
        }
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //https请求
        if (strpos($url, 'https') == 0) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        }
        //设置header
        if (!empty($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        //basic认证
        if (!empty($basic_auth)) {
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, $basic_auth['username'] . ':' . $basic_auth['passwork']);
        }
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //返回获得的数据
        if ($is_array) {
            return json_decode($data, true);
        } else {
            return $data;
        }
    }

    /**
     * post请求
     * @param  string  $url         请求的url,url中不可以带有参数,参数请在$params中设置
     * @param  array   $params      请求参数
     * @param  boolean $is_array    是否将结果转化为array形式返回
     * @param  array   $header      设置的header
     * @param  array   $basic_auth  array('username'=>'', 'passwork'=>'')
     * @return array/string         如果设置$is_array为true则返回array,false则直接返回请求结果
     */
    public function post($url, $params, $is_array = false, $headers = null, $basic_auth = null)
    {
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        //设置post数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        //https请求
        if (strpos($url, 'https') == 0) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        }
        //设置header
        if (!empty($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        //basic认证
        if (!empty($basic_auth)) {
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, $basic_auth['username'] . ':' . $basic_auth['passwork']);
        }
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //返回获得的数据
        if ($is_array) {
            return json_decode($data, true);
        } else {
            return $data;
        }
    }

    /**
     * put请求
     * @param  string  $url         请求的url,url中不可以带有参数,参数请在$params中设置
     * @param  array   $params      请求参数
     * @param  boolean $is_array    是否将结果转化为array形式返回
     * @param  array   $header      设置的header
     * @param  array   $basic_auth  array('username'=>'', 'passwork'=>'')
     * @return array/string         如果设置$is_array为true则返回array,false则直接返回请求结果
     */
    public function put($url, $params, $is_array = false, $headers = null, $basic_auth = null)
    {
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置put方式提交
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        //设置put数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        //https请求
        if (strpos($url, 'https') == 0) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        }
        //设置header
        if (!empty($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        //basic认证
        if (!empty($basic_auth)) {
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, $basic_auth['username'] . ':' . $basic_auth['passwork']);
        }
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //返回获得的数据
        if ($is_array) {
            return json_decode($data, true);
        } else {
            return $data;
        }
    }

    /**
     * delete请求
     * @param  string  $url         请求的url,url中不可以带有参数,参数请在$params中设置
     * @param  array   $params      请求参数
     * @param  boolean $is_array    是否将结果转化为array形式返回
     * @param  array   $header      设置的header
     * @param  array   $basic_auth  array('username'=>'', 'passwork'=>'')
     * @return array/string         如果设置$is_array为true则返回array,false则直接返回请求结果
     */
    public function delete($url, $params, $is_array = false, $headers = null, $basic_auth = null)
    {
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置delete方式提交
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        //设置post数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        //https请求
        if (strpos($url, 'https') == 0) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        }
        //设置header
        if (!empty($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        //basic认证
        if (!empty($basic_auth)) {
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_USERPWD, $basic_auth['username'] . ':' . $basic_auth['passwork']);
        }
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //返回获得的数据
        if ($is_array) {
            return json_decode($data, true);
        } else {
            return $data;
        }
    }
}
