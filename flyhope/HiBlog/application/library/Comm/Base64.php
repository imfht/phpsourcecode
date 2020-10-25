<?php
/**
 * Base64编码方法
 *
 * @package Comm
 * @author chengxuan <i@chengxuan.li>
 */
namespace Comm;
abstract class Base64 {
    
    /**
     * URL安全Base64方法
     * 
     * @param string $plain_text
     * 
     * @return string
     */
    static public function urlEncode($plain_text) {
        $base64 = base64_encode($plain_text);
        $base64url = strtr($base64, '+/', '-_');
        return $base64url;
    }
}