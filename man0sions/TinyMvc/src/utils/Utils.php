<?php
/**
 * Created by PhpStorm.
 * User: man0sions
 * Date: 16/8/29
 * Time: 下午3:22
 * 该类中包含了一些常用的函数和方法
 */

namespace LuciferP\TinyMvc\utils;

class Utils{

    /**
     * @param $data
     * @param $key
     * @return string
     */
    public static function  encrypt($data, $key)
    {
        $char = $str = '';
        $key    =   md5($key);
        $x      =   0;
        $len    =   strlen($data);
        $l      =   strlen($key);
        for ($i = 0; $i < $len; $i++)
        {
            if ($x == $l)
            {
                $x = 0;
            }
            $char .= $key{$x};
            $x++;
        }
        for ($i = 0; $i < $len; $i++)
        {
            $str .= chr(ord($data{$i}) + (ord($char{$i})) % 256);
        }
        return base64_encode($str);
    }

    /**
     * @param $data
     * @param $key
     * @return string
     */

    public static function decrypt($data, $key)
    {
        $char = $str = '';

        $key = md5($key);
        $x = 0;
        $data = base64_decode($data);
        $len = strlen($data);
        $l = strlen($key);
        for ($i = 0; $i < $len; $i++)
        {
            if ($x == $l)
            {
                $x = 0;
            }
            $char .= substr($key, $x, 1);
            $x++;
        }
        for ($i = 0; $i < $len; $i++)
        {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1)))
            {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            }
            else
            {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return $str;
    }
}