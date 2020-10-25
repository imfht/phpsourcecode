<?php
class EncodeUtils {

    
    /*
     * URL安全的Base64编码适用于以URL方式传递Base64编码结果的场景。
     * 该编码方式的基本过程是先将内容以Base64格式编码为字符串，
     * 然后检查该结果字符串，将字符串中的加号+换成中划线-，并且将斜杠/换成下划线_，同时尾部去除等号padding。
    */
    public static function encodeWithURLSafeBase64($arg)
    {
        if ($arg === null || empty($arg)) {
            return null;
        }
        $result = preg_replace(array("/\r/", "/\n/"), "", rtrim(base64_encode($arg), '=' )); 
        return $result;
    }  

}