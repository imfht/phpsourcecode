<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\util;

/**
 * String class file
 * 字符串处理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: String.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.util
 * @since 1.0
 */
class String
{
    /**
     * 清理字符串中的Xss攻击字符
     * @param string $param
     * @return string
     */
    public static function escapeXss($param)
    {
        $param = preg_replace("/[\r\n]+/", ' ', $param);
        return htmlspecialchars(trim($param));
    }

    /**
     * 替换字符串或字符串数组中的HTML代码为特殊字符
     * @param mixed $param
     * @return mixed
     */
    public static function specialchars_encode($param)
    {
        static $replaces = array(
            '&amp;' => '&',
            '&quot;' => '"',
            '&lt;' => '<',
            '&gt;' => '>',
        );

        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $param[$key] = self::specialchars_encode($value);
            }
        }
        else {
            $param = strtr($param, $replaces);
        }

        return $param;
    }

    /**
     * 替换字符串或字符串数组中的特殊字符为HTML代码
     * @param mixed $param
     * @return mixed
     */
    public static function specialchars_decode($param)
    {
        static $replaces = array(
            '&' => '&amp;',
            '"' => '&quot;',
            '<' => '&lt;',
            '>' => '&gt;',
        );

        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $param[$key] = self::specialchars_decode($value);
            }
        }
        else {
            $param = strtr($param, $replaces);
        }

        return $param;
    }

    /**
     * 使用反斜线引用字符串或字符串数组
     * @param mixed $param
     * @return mixed
     */
    public static function addslashes($param)
    {
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $param[$key] = self::addslashes($value);
            }
        }
        else {
            $param = addslashes($param);
        }

        return $param;
    }

    /**
     * 清除字符串或字符串数组的反斜线引用
     * @param mixed $param
     * @return mixed
     */
    public static function stripslashes($param)
    {
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $param[$key] = self::stripslashes($value);
            }
        }
        else {
            $param = stripslashes($param);
        }

        return $param;
    }

    /**
     * Base64方式加密字符串或字符串数组
     * @param mixed $param
     * @return mixed
     */
    public static function base64_encode($param)
    {
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $param[$key] = self::base64_encode($value);
            }
        }
        else {
            $param = base64_encode($param);
        }

        return $param;
    }

    /**
     * Base64方式解密字符串或字符串数组
     * @param mixed $param
     * @return mixed
     */
    public static function base64_decode($param)
    {
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $param[$key] = self::base64_decode($value);
            }
        }
        else {
            $param = base64_decode($param);
        }

        return $param;
    }

    /**
     * 编码URL串或URL数组
     * @param mixed $param
     * @return mixed
     */
    public static function urlencode($param)
    {
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $param[$key] = self::urlencode($value);
            }
        }
        else {
            $param = urlencode($param);
        }

        return $param;
    }

    /**
     * 解码URL串或URL数组
     * @param mixed $param
     * @return mixed
     */
    public static function urldecode($param)
    {
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $param[$key] = self::urldecode($value);
            }
        }
        else {
            $param = urldecode($param);
        }

        return $param;
    }

    /**
     * 获取随机字符串，不取1和l、0和O，因为这些字符容易混淆
     * @param integer $length
     * @param string $format 字符格式：ALL(英文字母加数字)、LETTER(只字母)、NUMBER(只数字)
     * @return string
     */
    public static function randStr($length = 4, $format = 'ALL')
    {
        return implode('', self::randChars($length, $format));
    }

    /**
     * 获取随机字符，不取1和l、0和O，因为这些字符容易混淆
     * @param integer $length 字符个数
     * @param string $format 字符格式：ALL(英文字母加数字)、LETTER(只字母)、NUMBER(只数字)
     * @return array
     */
    public static function randChars($length = 4, $format = 'ALL')
    {
        static $chars = 'ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
        $start = $format == 'NUMBER' ? 49 : 0;
        $end   = $format == 'LATTER' ? 48 : 56;
        $rand  = array();
        for ($i = 0; $i < $length; $i++) {
            $pos = mt_rand($start, $end);
            $rand[] = $chars{$pos};
        }

        return $rand;
    }

    /**
     * 获取字符串的ASCII码总和
     * @param string $string
     * @return integer
     */
    public static function ascii($string)
    {
        $output = 0;

        $string = (string) $string;
        $length = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $output += ord($string{$i});
        }

        return $output;
    }
}
