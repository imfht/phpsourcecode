<?php

/*字符串验证*/

class Simplevalidate
{
    //构造方法
    public function __construct()
    {

    }

    //验证是否为空字符串
    public function required($str)
    {
        $str = trim($str);
        if ($str == '') {
            return false;
        }
        return true;
    }

    //验证邮箱
    public function email($str)
    {
        return filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    //验证昵称
    public function nickname($str)
    {
        if (preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9-]+$/u', $str)) {
            return true;
        }
        return false;
    }

    //验证手机号
    public function mobile($str)
    {
        if (preg_match('/^1[34578]\d{9}$/', $str)) {
            return true;
        }
        return false;
    }

    //验证QQ号
    public function qq($str)
    {
        if (preg_match('/[1-9][0-9]{4,10}/', $str)) {
            return true;
        }
        return false;
    }

    //验证域名
    public function domain($str)
    {
        if (preg_match('/[a-zA-Z0-9][-a-zA-Z0-9]{0,62}(\.[a-zA-Z0-9][-a-zA-Z0-9]{0,62})+\.?/', $str)) {
            return true;
        }
        return false;
    }

    //长度范围
    public function range($str, $min, $max)
    {
        $len = strlen($str);
        if ($len >= $min && $len < $max) {
            return true;
        }
        return false;
    }

    //长度范围(中英文混合)
    public function mix_range($str, $min, $max)
    {
        $len = (strlen($str) + mb_strlen($str, 'utf8')) / 2;
        if ($len >= $min && $len <= $max) {
            return true;
        }
        return false;
    }

    //验证0或大于0的整数
    public function number($str)
    {
        if (preg_match('/^(0|[1-9][0-9]*)$/', $str)) {
            return true;
        }
        return false;
    }
}
