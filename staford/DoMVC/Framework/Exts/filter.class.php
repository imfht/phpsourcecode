<?php

/**
 * 数据过滤验证器
 * @author 暮雨秋晨
 * @copyright 2014
 */

class Filter
{
    public static function regex($string, $regexp)
    {
        if (filter_var($string, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" =>
                    "{$regexp}")))) {
            return true;
        } else {
            return false;
        }
    }

    public static function mail($str)
    {
        if (filter_var($str, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    public static function url($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return true;
        } else {
            return false;
        }
    }

    public static function ip($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return true;
        } else {
            return false;
        }
    }

    public static function num($num)
    {
        if (preg_match('![^\d]!Ui', $num)) {
            return false;
        } else {
            return true;
        }
    }
}

?>