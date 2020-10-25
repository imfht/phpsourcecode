<?php
namespace utils;


use Firebase\JWT\JWT;

class JWTUtils {

    /**
     * 加密
     * @param $name
     * @return string
     */
    public static function encode($name)
    {
        return JWT::encode($name, config('app_key'));
    }

    /**
     * 解密
     * @param $name
     * @return string
     */
    public static function decode($name)
    {
        return JWT::decode($name, config('app_key'), array('HS256'));
    }

}