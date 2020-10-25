<?php defined('BASEPATH') OR exit('No direct script access allowed');
defined('REGEXP_SHA1') OR define('REGEXP_SHA1', "/^(\S){40}$/"); // SHA1 密文
defined('REGEXP_MD5') OR define('REGEXP_MD5', "/^(\S){32}$/"); // MD5 密文

defined('REGEXP_ACCOUNT') OR define('REGEXP_ACCOUNT', "/^[a-zA-Z0-9_]{4,20}$/"); // 颜色哈希代码
defined('REGEXP_COLOR_HEX') OR define('REGEXP_COLOR_HEX', "/^#([a-fA-F0-9]){3}(([a-fA-F0-9]){3})?$/"); // 颜色哈希代码
defined('REGEXP_IPV4') OR define('REGEXP_IPV4', "/^(\d|[01]?\d\d|2[0-4]\d|25[0-5])\.(\d|[01]?\d\d|2[0-4] \d|25[0-5])\.(\d|[01]?\d\d|2[0-4]\d|25[0-5])\.(\d|[01]?\d\d|2[0-4] \d|25[0-5])$/"); // IPv4 地址
defined('REGEXP_EMAIL') OR define('REGEXP_EMAIL', "/^[A-Za-z0-9]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/"); // Email
defined('REGEXP_MAC_ADDRESS') OR define('REGEXP_MAC_ADDRESS', "/^([0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}$/"); // MAC 地址
defined('REGEXP_QQ') OR define('REGEXP_QQ', "/^[1-9][0-9]{4,}$/"); // QQ 号
defined('REGEXP_URL') OR define('REGEXP_URL', "/^(ht|f)tp(s?)\:\/\/[0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*(:(0-9)*)*(\/?)([a-zA-Z0-9\-\.\?\,\'\/\\\+&amp;%$#_]*)?$/"); // URL 地址

defined('REGEXP_CN_CID') OR define('REGEXP_CN_CID', "/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X|x)$/"); // 中国身份证号码
defined('REGEXP_CN_PHONE') OR define('REGEXP_CN_PHONE', "/^1[34578]\d{9}$/"); // 中国大陆手机号码
defined('REGEXP_CN_POSTCODE') OR define('REGEXP_CN_POSTCODE', "/^[1-9]\d{5}(?!\d)$/"); // 中国邮政编码
defined('REGEXP_CN_TEL') OR define('REGEXP_CN_TEL', "/\d{3}-\d{8}|\d{4}-\d{7,8}/"); // 中国座机号码
defined('REGEXP_CN_UTF8') OR define('REGEXP_CN_UTF8', "/[\u4e00-\u9fa5]/"); // 中文字符utf-8编码

if (!function_exists('regexp')) {
    /**
     * 正则数据验证
     * @param $rule
     * @param $str
     * @return false|int
     */
    function regexp($rule, $str) {
        return preg_match(constant('REGEXP_' . strtoupper($rule)), $str);
    }
}