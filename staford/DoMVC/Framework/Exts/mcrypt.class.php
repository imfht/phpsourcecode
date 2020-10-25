<?php

/**
 * 加密解密类
 * @author 暮雨秋晨
 * @copyright 2014
 */

class Mcrypt
{
    private static $str_key; //加密密文
    private static $str_tag; //密文标记

    /**
     * 初始化环境配置
     */
    public static function set_config($cfg)
    {
        $tag = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $str = base64_encode(trim($cfg));
        $len = strlen($str);
        self::$str_key = substr(substr($str, rand(0, floor($len / 2)), rand(floor($len /
            2), $len)), 0, 1);
        self::$str_tag = $tag[rand(0, strlen($tag) - 1)];
    }

    /**
     * 加密数据
     * @param string $str 字符串
     * @return mixed 加密后的内容
     */
    public function encrypt($str, $level = 1)
    {
        $level = $level > 0 ? $level : 1;
        for ($i = 0; $i < $level; $i++) {
            $str = self::_encrypt($str);
        }
        return $str;
    }

    /**
     * 解密函数
     * @param string $str 密文
     * @return mixed 解密后的内容
     */
    public function decrypt($str, $level = 1)
    {
        $level = $level > 0 ? $level : 1;
        for ($i = 0; $i < $level; $i++) {
            $str = self::_decrypt($str);
        }
        return $str;
    }

    private static function _encrypt($str)
    {
        return str_replace(self::$str_tag, '//' . self::$str_key, base64_encode($str));
    }

    private static function _decrypt($str)
    {
        return base64_decode(str_replace('//' . self::$str_key, self::$str_tag, $str));
    }
}

?>