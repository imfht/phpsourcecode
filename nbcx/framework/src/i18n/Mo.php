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

use nb\i18n\assist\Gettext;

/**
 * Mo
 *
 * @package nb\i18n
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2018/1/19
 */
class Mo extends Driver {

    /**
     * 所有的文件读写句柄
     *
     * @access private
     * @var array Mo
     */
    private $lang = [];

    /**
     * 其它语言信息
     * @var string
     */
    private $range = [];

    /**
     * 默认配置
     * @var array
     */
    protected $config = [
        'path'=>'',
        'default_lang'=>'',
        'enable_cache'=>true
    ];

    /**
     * 构造函数
     *
     * @access public
     * @param string $fileName 语言文件名
     * @return void
     */
    public function __construct(array $config = []) {
        $config = array_merge(
            $this->config,
            $config
        );
        //$file, $enable_cache
        $file = $config['path'].$config['default_lang'];
        $this->handles[] = new Gettext($file,$config['enable_cache']);


        $this->config = $config;
        //$this->addFile($config);

        /*
        if(isset($config['path'])) {
            $this->addFile($config['path']);
        }
        else {
            trigger_error('Momulti`s path no set!');
        }
        */
        /** 语言包初始化 */
        //NConfig::getx('lang') == 'zh_CN' or NI18n::setLang(NConfig::getx('path_lang') . NConfig::getx('lang') . '.mo');
    }

    /**
     * 增加一个语言文件
     *
     * @access public
     * @param string $fileName 语言文件名
     * @return void
     */
    public function load($file, $range = '') {
        // 批量定义
        if (is_string($file)) {
            $file = [$file];
        }

        $lang = [];


        $enable_cache=$this->config['enable_cache'];
        foreach ($file as $v) {
            //$file,需要判断是默认目录，还是完整目录
            $lang[] = new Gettext($v,$enable_cache);
        }

        if($range) {
            if (!isset($this->range[$range])) {
                $this->range[$range] = [];
            }
            $this->range[$range] = array_merge_recursive($lang,$this->range[$range]);//$lang + $this->range[$range];
        }
        else {
            $this->lang = array_merge_recursive($lang,$this->lang);//$lang + $this->lang;
        }
    }


    /**
     * Translates a string
     *
     * @access public
     * @param string string to be translated
     * @return string translated string (or original, if not found)
     */
    public function get($string = null, $range = '') {
        if($range) {
            $lang = isset($this->range[$range]) ? $this->range[$range] : [];
        }
        else {
            $lang = $this->lang;
        }

        foreach ($lang as $lg) {
            $count = 0;
            $string = $lg->translate($string, $count);
            if (-1 != $count) {
                break;
            }
        }

        return $string;
    }

    /**
     * Plural version of gettext
     *
     * @access public
     * @param string single
     * @param string plural
     * @param string number
     * @return translated plural form
     */
    public function ngettext($single, $plural, $number) {
        $count = 0;
        foreach ($this->handles as $handle) {
            $string = $handle->ngettext($single, $plural, $number, $count);
            if (-1 != $count) {
                break;
            }
        }

        return $string;
    }
}
