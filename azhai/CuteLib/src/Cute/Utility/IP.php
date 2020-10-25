<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Utility;


defined('IP_ECHO_URL') or define('IP_ECHO_URL', 'http://ipecho.net/plain');


/**
 * IP地址
 */
class IP
{
    const BYTE_IP_V4 = 4;      //IPv4长度，4字节
    const BYTE_IP_V6 = 6;      //IPv6长度，6字节

    protected $ipaddr = '';

    /**
     * 构造函数
     */
    public function __construct($ipaddr = false)
    {
        $this->ipaddr = $ipaddr ?: self::getClientIP();
    }

    /**
     * 可读格式
     */
    public function __toString()
    {
        return $this->ipaddr;
    }

    /**
     * 将字符串IP转为HEX格式
     */
    public static function toHex($ipaddr)
    {
        return sprintf('%08x', ip2long($ipaddr));
    }

    /**
     * 获取真实HTTP客户端IP，按次序尝试
     * @return string 客户端IP地址
     */
    public static function getClientIP()
    {
        $keys = [
            'HTTP_CLIENT_IP', 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR',
        ];
        foreach ($keys as $key) {
            $ipaddr = isset($_SERVER[$key]) ? $_SERVER[$key] : '';
            if ($ipaddr && strlen($ipaddr) >= 7) {
                return $ipaddr;
            }
        }
    }

    /**
     * 获取服务器外网IP
     * @return string 服务器外网IP
     */
    public static function getServerIP()
    {
        if ($ipaddr = file_get_contents(IP_ECHO_URL)) {
            return trim($ipaddr);
        }
    }

    /**
     * 解析出正确的IP，用于域名访问超时
     * @return string 域名对应IP地址
     */
    public static function getHostIP($domain)
    {
        $dns_records = dns_get_record($domain, DNS_A, $authns, $addtl);
        if ($dns_records && $ipaddr = $dns_records[0]['ip']) {
            return $ipaddr; //使用解析到的IP
        } else {
            return \app()->getStandbyHost($domain);
        }
    }
}
