<?php
namespace ebcms;

class Curl
{
    public static function post($url, $data, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //运行curl
        $res = curl_exec($ch);
        //返回结果
        if (false !== $res) {
            curl_close($ch);
            return $res;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            return json_encode(['code' => 0, 'msg' => 'curl出错，错误码：' . $error, 'url' => '', 'data' => []]);
        }
    }
}