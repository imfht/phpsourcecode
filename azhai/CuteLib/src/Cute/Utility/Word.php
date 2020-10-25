<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Utility;


/**
 * 单词转换
 * NOTICE:
 *      mb_strlen('〇一二三四五六七八九十百千万亿') = 45
 *      mb_strlen('〇一二三四五六七八九十百千万亿', 'UTF-8') = 15
 *      mb_strwidth('〇一二三四五六七八九十百千万亿') = 45
 *      mb_strwidth('〇一二三四五六七八九十百千万亿', 'UTF-8') = 30
 */
class Word
{
    public static $digits = '0123456789';
    public static $chars = '〇一二三四五六七八九十百千万亿';
    public static $caps = '零壹贰叁肆伍陆柒捌玖拾佰仟萬億';
    protected $content = '';

    /**
     * 构造函数
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }

    /**
     * 数字转为中文
     */
    public static function num2char($num, $capital = false)
    {
        $alts = $capital ? self::$caps : self::$chars;
        return self::mbStrtr(strval($num), self::$digits, $alts);
    }

    /**
     * UTF-8的汉字替换
     */
    public static function mbStrtr($string, $from, $to)
    {
        $from = self::mbStrSplit($from);
        $to = self::mbStrSplit($to);
        return str_replace($from, $to, $string);
    }

    /**
     * UTF-8的汉字切分
     */
    public static function mbStrSplit($string)
    {
        $width = self::hasNonASCII($string) ? 3 : 1;
        return str_split($string, $width);
    }

    /**
     * 含有非ASCII字符
     */
    public static function hasNonASCII($string)
    {
        return preg_match('/[^\x20-\x7f]/', $string);
    }

    /**
     * 数值转为中文拼读
     */
    public static function spell($number, $capital = false)
    {
        $formatter = new \NumberFormatter('zh_CN',
            \NumberFormatter::SPELLOUT);
        $sentence = $formatter->format($number);
        if ($capital) {
            $sentence = self::mbStrtr($sentence, self::$chars, self::$caps);
        }
        return $sentence;
    }

    /**
     * 将内容字符串中的变量替换掉
     * @param string $content 内容字符串
     * @param array $context 变量数组
     * @param string $prefix 变量前置符号
     * @param string $subfix 变量后置符号
     * @return string 当前内容
     */
    public static function replaceWith($content, array $context = [],
                                       $prefix = '', $subfix = '')
    {
        if (empty($context)) {
            return $content;
        }
        if (empty($prefix) && empty($subfix)) {
            $replacers = &$context;
        } else {
            $replacers = [];
            foreach ($context as $key => & $value) {
                $replacers[$prefix . $key . $subfix] = $value;
            }
        }
        $content = strtr($content, $replacers);
        return $content;
    }

    /**
     * 产生16进制随机字符串
     */
    public static function randHash($length = 6)
    {
        $length = $length > 32 ? 32 : $length;
        $hash = md5(mt_rand() . time());
        $buffers = substr($hash, 0, $length);
        return $buffers;
    }

    /**
     * 产生可识别的随机字符串
     */
    public static function randString($length = 6, $shuffles = 2, $good_letters = '')
    {
        if (empty($good_letters)) {
            // 字符池，去掉了难以分辨的0,1,o,O,l,I
            $good_letters = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        }
        srand((float)microtime() * 1000000);
        $buffer = '';
        // 每次可以产生的字符串最大长度
        $gen_length = ceil($length / $shuffles);
        while ($length > 0) {
            $good_letters = str_shuffle($good_letters);
            $buffer .= substr($good_letters, 0, $gen_length);
            $length -= $gen_length;
            $gen_length = min($length, $gen_length);
        }
        return $buffer;
    }

    /**
     * 提取第一条网址
     */
    public function fetchFirstURL()
    {
        if (preg_match('/^http[^\x23-\x76]/i', $this->content, $matches)) {
            return $matches[1]; //已经排除空格\x20
        }
    }

    /**
     * 将版本号转为整数，版本号分为三段
     */
    public function ver2int()
    {
        $version = $this->getNumbers(false);
        $vernums = array_map('intval', explode('.', $version)); //将点号分隔的版本号转为整数
        $vernums = array_pad($vernums, 3, 0);
        return intval(vsprintf('%d%02d%02d', $vernums));
    }

    /**
     * 保留字符串中的数字和小数点
     */
    public function getNumbers($to_int = true)
    {
        $times = preg_match_all('/[\d.]+/', $this->content, $matches);
        if ($times === 0 || $times === false) {
            return false;
        }
        $number = implode(current($matches));
        return $to_int ? intval($number) : $number;
    }
}
