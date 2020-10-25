<?php
/**
 * 获取客户端真实IP地址
 */
if (!function_exists('client_ip')) {
    function client_ip() {
        $ip = 'unknown';
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_XFORWARDED_FOR')) {
                $ip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $ip = getenv('HTTP_CLIENT_IP');
            } else {
                $ip = getenv('REMOTE_ADDR');
            }
        }
        if (trim($ip) == "::1") {
            $ip = '127.0.0.1';
        }
        return $ip;
    }
}