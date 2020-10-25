<?php
namespace utils;


use Firebase\JWT\JWT;

class SessionUtils
{
    /**
     * Auth与AuthSign存入session
     * @param  array $data auth数据
     * @param  array $abilitiesAuth auth_abilities数据
     * @author haigle <991382548@qq.com>
     */
    public function setSessionAuth($data, $abilitiesAuth)
    {

        $auth_sign = $this->auth_sign($data);
        session('auth', JWT::encode($data, config('app_key')));
        session('auth_abilities', JWT::encode($abilitiesAuth, config('app_key')));
        session('auth_sign', $auth_sign);
//        session('we',JWT::encode('999', config('app_key')));
        return true;

    }

    public function sessionOut()
    {
        session(null);
    }

    /**
     * 数据签名认证
     * @param  array $data 被认证的数据
     * @return string       签名
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    private function auth_sign($data)
    {
        //数据类型检测
        if (!is_array($data)) {
            $data = (array)$data;
        }
        ksort($data); //排序
        $code = http_build_query($data); //url编码并生成query字符串
        $sign = sha1($code); //生成签名
        return $sign;
    }

}