<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb;

/**
 * 国际化字符翻译
 *
 * @package nb
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/7
 */
class I18n extends Component {

    /**
     * 语言文件
     *
     * @access private
     * @var string
     */
    private static $enable = false;

    public static function config() {
        $conf = Config::$o->i18n;
        if($conf) {
            self::$enable = true;
        }
        return $conf;
    }

    /**
     * 翻译文字
     *
     * @access public
     * @param string $string 待翻译的文字
     * @return string
     */
    public static function t($string, $vars = []) {
        return self::tx('',$string, $vars);
    }

    /**
     * 翻译文字
     * 从给定区域获取原文
     *
     * @param $range
     * @param $string
     * @param array $vars
     */
    public static function tx($range, $string, $vars = []) {
        $value = self::driver()->get($string,$range);

        // 变量解析
        if (!empty($vars) && is_array($vars)) {
            /**
             * Notes:
             * 为了检测的方便，数字索引的判断仅仅是参数数组的第一个元素的key为数字0
             * 数字索引采用的是系统的 sprintf 函数替换，用法请参考 sprintf 函数
             */
            if (key($vars) === 0) {
                // 数字索引解析
                array_unshift($vars, $value);
                $value = call_user_func_array('sprintf', $vars);
            }
            else {
                // 关联索引解析
                $replace = array_keys($vars);
                foreach ($replace as &$v) {
                    $v = "{:{$v}}";
                }
                $value = str_replace($replace, $vars, $value);
            }
        }

        return $value;
    }

    /**
     * 动态加载语言文件
     */
    public static function load($file, $range = '') {
        self::driver()->load($file,$range);
    }

    /**
     * 翻译文字
     *
     * @access public
     * @param string $string 待翻译的文字
     * @return string
     */
    public static function translate($string) {
        return self::$enable?self::driver()->translate($string):$string;
    }

    /**
     * 针对复数形式的翻译函数
     *
     * @param string $single 单数形式的翻译
     * @param string $plural 复数形式的翻译
     * @param integer $number 数字
     * @return string
     */
    public function ngettext($single, $plural, $number) {
        return Config::$o->lang?self::driver()->ngettext($single, $plural, $number) : ($number > 1 ? $plural : $single);
        //self::init();
        //return self::$_lang ? self::$_loaded->ngettext($single, $plural, $number) : ($number > 1 ? $plural : $single);
    }

    /**
     * 词义化时间
     *
     * @access public
     * @param string $from 起始时间
     * @param string $now 终止时间
     * @return string
     */
    public static function dateWord($from, $now) {
        $between = $now - $from;
        /** 如果是一天 */
        if ($between >= 0 && $between < 86400 && date('d', $from) == date('d', $now)) {
            /** 如果是一小时 */
            if ($between < 3600) {
                /** 如果是一分钟 */
                if ($between < 60) {
                    if (0 == $between) {
                        return _t('刚刚');
                    }
                    else {
                        return str_replace('%d', $between, _n('一秒前', '%d秒前', $between));
                    }
                }

                $min = floor($between / 60);
                return str_replace('%d', $min, _n('一分钟前', '%d分钟前', $min));
            }

            $hour = floor($between / 3600);
            return str_replace('%d', $hour, _n('一小时前', '%d小时前', $hour));
        }

        /** 如果是昨天 */
        if ($between > 0 && $between < 172800
            && (date('z', $from) + 1 == date('z', $now)                             // 在同一年的情况
                || date('z', $from) + 1 == date('L') + 365 + date('z', $now))
        ) {    // 跨年的情况
            return _t('昨天 %s', date('H:i', $from));
        }

        /** 如果是一个星期 */
        if ($between > 0 && $between < 604800) {
            $day = floor($between / 86400);
            return str_replace('%d', $day, _n('一天前', '%d天前', $day));
        }

        /** 如果是 */
        if (date('Y', $from) == date('Y', $now)) {
            return date(_t('n月j日'), $from);
        }

        return date(_t('Y年m月d日'), $from);
    }

    /**
     * 增加语言项
     *
     * @access public
     * @param string $lang 语言名称
     * @return void
     */
    //public function addLang($lang) {
        //Config::$o->lang?self::driver()->addFile($lang);
    //}

    //public function getLang() {
//
    //}

}