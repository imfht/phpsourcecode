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

use tfc\ap\ErrorException;
use tfc\ap\Ap;

/**
 * Encoder class file
 * 字符编码处理类，只处理GBK或UTF-8编码
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Encoder.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.util
 * @since 1.0
 */
class Encoder
{
    /**
     * @var string UTF-8编码
     */
    const CHARSET_UTF8 = 'UTF-8';

    /**
     * @var string GBK编码
     */
    const CHARSET_GBK  = 'GBK';

    /**
     * @var string 当前项目的编码方式
     */
    protected $_charset = self::CHARSET_UTF8;

    /**
     * @var instances of tfc\util\Encoder
     */
    protected static $_instances = array();

    /**
     * 构造方法：初始化本项目的编码方式，计算字符长度时，以此编码为准；转换字符时，此编码是默认转出编码
     * @param string $charset 'UTF-8' or 'GBK'
     * @throws ErrorException 如果指定的编码不是UTF8或GBK，抛出异常
     */
    protected function __construct($charset)
    {
        if (!defined('static::CHARSET_' . str_replace('-', '', $charset))) {
            throw new ErrorException(sprintf(
                'Encoder charset "%s" must be UTF-8 or GBK', $charset
            ));
        }

        $this->_charset = $charset;
    }

    /**
     * 魔术方法：禁止被克隆
     */
    private function __clone()
    {
    }

    /**
     * 单例模式：获取本类的实例化对象
     * @param string $charset
     * @return \tfc\util\Encoder
     */
    public static function getInstance($charset = null)
    {
        if ($charset === null) {
            $charset = Ap::getEncoding();
        }

        $charset = strtoupper($charset);
        if (!isset(self::$_instances[$charset])) {
            self::$_instances[$charset] = new self($charset);
        }

        return self::$_instances[$charset];
    }

    /**
     * 字符单字节截串，为了优化页面展示，一个汉字按两个字符计算，如果第三个参数小于0，则默认是字符长度
     * @param string $input
     * @param integer $start
     * @param integer $length
     * @return string
     */
    public function substr($input, $start = 0, $length = -1)
    {
        $input = String::specialchars_encode($input);
        $iptLen = strlen($input);
        if ($iptLen <= 0) {
            return '';
        }

        if ($length < 0) {
            $length = $iptLen;
        }

        $end = $start + $length;
        if ($start == 0) {
            $end++;
        }

        $pos = $charLen = $noc = 0;
        $posLessStart = true;
        while ($pos < $iptLen) {
            $charLen = $this->charlen($input[$pos]);
            $pos += ($charLen > 0) ? $charLen : 1;
            $noc += ($charLen > 1) ? 2 : (($charLen > 0) ? 1 : 0);
            if ($posLessStart && $noc >= $start) {
                if ($noc === $start) {
                    $start = $pos;
                }
                else {
                    $start = $pos - $charLen;
                    $end--;
                }
                $posLessStart = false;
            }

            if ($noc >= $end) {
                break;
            }
        }

        if ($noc > $end) {
            $pos -= $charLen;
        }

        $output = substr($input, $start, $pos - $start);
        $output = String::specialchars_decode($output);
        return $output;
    }

    /**
     * 根据字符的ASCII编码计算字符串的长度，一个汉字按两个字符计算
     * @param string $param
     * @return integer
     */
    public function strlen($param)
    {
        $iptLen = strlen($param);
        $pos = $charLen = $length = 0;
        while ($pos < $iptLen) {
            $charLen = $this->charlen($param[$pos]);
            $pos += ($charLen > 0) ? $charLen : 1;
            $length += ($charLen > 1) ? 2 : (($charLen > 0) ? 1 : 0);
        }

        return $length;
    }

    /**
     * 根据字符的ASCII编码计算字符的长度，一个汉字按两个字符计算
     * @param char $param
     * @return integer
     */
    public function charlen($param)
    {
        $asc = ord($param);
        if (($asc == 9) || ($asc == 10) || ($asc >= 32 && $asc <= 126)) {
            return 1;
        }

        if ($asc <= 127) {
            return 0;
        }

        if ($this->isGbk()) {
            return 2;
        }

        switch (true) {
            case ($asc >= 194 && $asc <= 223):
                return 2;
            case ($asc >= 224 && $asc <= 239):
                return 3;
            case ($asc >= 240 && $asc <= 247):
                return 4;
            case ($asc >= 248 && $asc <= 251):
                return 5;
            case ($asc == 252 || $asc == 253):
                return 6;
            default:
                return 2;
        }
    }

    /**
     * 获取当前项目的编码
     * @return string
     */
    public function getCharset()
    {
        return $this->_charset;
    }

    /**
     * 判断当前项目的编码是否是GBK格式
     * @return boolean
     */
    public function isGbk()
    {
        return $this->_charset === self::CHARSET_GBK;
    }

    /**
     * 判断当前项目的编码是否是UTF8格式
     * @return boolean
     */
    public function isUtf8()
    {
        return $this->_charset === self::CHARSET_UTF8;
    }

    /**
     * 转换字符串或字符串数组的编码
     * 优先采用mb_convert_encoding函数，如果mb_convert_encoding函数不存在，采用iconv函数
     * @param mixed $param
     * @param string $inCharset
     * @param string $outCharset
     * @return mixed
     * @throws ErrorException 如果mb_convert_encoding或者iconv函数不存在，抛出异常
     */
    public function convert($param, $inCharset = 'GBK', $outCharset = null)
    {
        if (function_exists('mb_convert_encoding')) {
            return Encoder::mb_convert_encoding($param, $inCharset, $outCharset);
        }

        if (function_exists('iconv')) {
            return Encoder::iconv($param, $inCharset, $outCharset);
        }

        throw new ErrorException(sprintf(
            'Encoder convert encoding failed, function "mb_convert_encoding" or "iconv" not exists'
        ));
    }

    /**
     * mb_convert_encoding方式转换字符串或字符串数组的编码
     * @param mixed $param
     * @param string $inCharset
     * @param string $outCharset
     * @return mixed
     */
    public function mb_convert_encoding($param, $inCharset = 'GBK', $outCharset = null)
    {
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $param[$key] = self::mb_convert_encoding($value, $inCharset, $outCharset);
            }
        }
        else {
            $param = mb_convert_encoding($param, ($outCharset === null ? $this->_charset : $outCharset), $inCharset);
        }

        return $param;
    }

    /**
     * iconv方式转换字符串或字符串数组的编码
     * @param mixed $param
     * @param string $inCharset
     * @param string $outCharset
     * @return mixed
     */
    public function iconv($param, $inCharset = 'GBK', $outCharset = null)
    {
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $param[$key] = self::iconv($value, $inCharset, $outCharset);
            }
        }
        else {
            $param = iconv($inCharset, ($outCharset === null ? $this->_charset : $outCharset), $param);
        }

        return $param;
    }
}
