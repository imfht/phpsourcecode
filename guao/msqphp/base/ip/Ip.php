<?php declare(strict_types = 1);
namespace msqphp\base\ip;

use msqphp\core\traits;

final class Ip
{
    use traits\CallStatic;

    // 扔出异常
    private static function exception(string $message) : void
    {
        throw new IpException($message);
    }

    private static $ip = '';
    private static $intip = 0;

    // Ip检测, 要求为合法的IPv4/v6 IP
    public static function check(string $ip) : bool
    {
        return false !== filter_var($ip, FILTER_VALIDATE_IP);
    }

    // 获取ip地址
    public static function get() : string
    {
        if (empty(static::$ip)) {

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
                if (false !== $pos = array_search('unknown', $arr)) {
                    unset($arr[$pos]);
                }
                $ip = trim($arr[0]);
                if (false !== $pos = strpos($ip, ',')) {
                    $ip = substr($ip, 0, $pos);
                }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            } else {
                static::exception('无法获取ip地址');
            }

            //  IP地址合法验证
            static::check($ip) || static::exception('无法获取合法ip地址');

            static::$ip = $ip;
        }

        return static::$ip;
    }
    // 获取ip地址
    public static function getString() : string
    {
        return static::get();
    }
    // 获得数字化后的ip
    public static function getInt() : int
    {
        return static::$intip = (0 === static::$intip ? ip2long(static::get()) : static::$intip);
    }
    // 将ip地址整形化
    public static function toInt(string $ip) : int
    {
        static::check($ip) || static::exception('非法ip,无法转换为整型');
        return ip2long($ip);
    }
    // 将整型ip地址还原
    public static function toString(int $ip) : string
    {
        return long2ip($ip);
    }
    public static function getHostByName(string $host) : string
    {
        return gethostbyname($host);
    }
    public static function getHostListByName(string $host) : array
    {
        return gethostbynamel($host);
    }
}