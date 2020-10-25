<?php
/**
 * YanPHP
 * User: weilongjiang(江炜隆)<willliam@jwlchina.cn>
 */

namespace Yan\Core;


class Input
{
    protected static $data = array();

    protected static $method;

    public static function initialize()
    {
        self::$method = strtoupper($_SERVER['REQUEST_METHOD']);
        parse_str(file_get_contents('php://input'), $input);
        $input = array_merge($input, $_REQUEST);
        $input = array_merge($input, $_FILES);

        self::$data = $input;

    }

    public static function get($key = '')
    {
        if (empty($key)) return self::$data;
        return isset(self::$data[$key]) ? self::$data[$key] : null;
    }

    public static function set($key, $value)
    {
        self::$data[$key] = $value;
    }

    public static function setData(array $data)
    {
        self::$data = $data;
    }

    public static function batchSet(array $d)
    {
        foreach ($d as $key => $value) {
            self::$data[$key] = $value;
        }
    }

}