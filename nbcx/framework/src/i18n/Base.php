<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb\i18n;

/**
 * Native
 *
 * @package nb\i18n
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2018/1/19
 */
class Base extends Driver {
    /**
     * 当前语言
     * @var array
     */
    private $lang = [];

    /**
     * 其它语言信息
     * @var string
     */
    private $range = [];

    public function __construct($config=[]) {
        isset($config['path']) and $this->load($config['path']);
    }


    /**
     * 加载语言定义
     * @access public
     * @param  string|array $file 语言文件
     * @param  string $range 语言作用域
     * @return array
     */
    public function load($file, $range = '') {
        //$range = $range ?: $this->range;
        //if (!isset($this->lang[$range])) {
        //    $this->lang[$range] = [];
        //}

        // 批量定义
        if (is_string($file)) {
            $file = [$file];
        }

        $lang = [];

        foreach ($file as $_file) {
            if (is_file($_file)) {
                // 记录加载信息
                $_lang = include $_file;
                if (is_array($_lang)) {
                    $lang = $_lang+ $lang;
                }
            }
        }

        if(!$lang) {
            return false;
        }

        if($range) {
            if (!isset($this->range[$range])) {
                $this->range[$range] = [];
            }
            $this->range[$range] = $lang + $this->range[$range];
        }
        else {
            $this->lang = $lang + $this->lang;
        }
    }

    /**
     * 获取语言定义
     * @access public
     * @param  string|null $name 语言变量
     * @param  string $range 语言作用域
     * @return bool
     */
    public function has($name, $range = '') {
        $range = $range ?: $this->range;

        return isset($this->lang[$range][$name]);
    }

    /**
     * 获取语言定义
     * @access public
     * @param  string|null $name 语言变量
     * @param  array $vars 变量替换
     * @param  string $range 语言作用域
     * @return mixed
     */
    public function get($string = null, $range = '') {
        if($range) {
            $value = isset($this->range[$range][$string]) ? $this->range[$range][$string] : $string;
        }
        else {
            $value = isset($this->lang[$string]) ? $this->lang[$string] : $string;
        }

        return $value;
    }


}
