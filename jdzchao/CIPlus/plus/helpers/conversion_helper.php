<?php defined('BASEPATH') OR exit('No direct script access allowed');

defined('DICT_36') or define('DICT_36', '0123456789abcdefghijklmnopqrstuvwxyz');
defined('DICT_62') or define('DICT_62', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

if (function_exists('decimal_encode')) {
    /**
     * 整型数字 -> N进制编码
     * @param $num
     * @param $rule
     * @param bool $customize
     * @return string
     */
    function decimal_encode($num, $rule = 36, $customize = false) {
        $dict = $customize ? $rule : constant('DICT_' . $rule);
        $len = strlen($dict);
        $k = (int)fmod($num, $len);// php使用“%”求余可能会溢出，使用“fmod()”函数
        $str = $dict[$k];
        if ($num >= $len) {
            $num = floor($num / $len);
            $str = $this->Encode($num, $rule) . $str;
        }
        return $str;
    }
}

if (function_exists('decimal_decode')) {
    /**
     * N进制编码 -> 整型数字
     * @param $str
     * @param $rule
     * @param bool $customize
     * @return bool|int
     */
    function decimal_decode($str, $rule = 36, $customize = false) {
        $rule = $customize ? $rule : constant('DICT_' . $rule);
        $len = strlen($rule);
        $n = strlen($str);
        $x = 0;
        for ($i = $n; $i > 0; $i--) {
            $x += strpos($rule, $str[$n - $i]) * pow($len, $i - 1);
        }
        return $x;
    }
}