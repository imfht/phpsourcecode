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
 * Driver
 *
 * @package nb\i18n
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2018/1/19
 */
abstract class Driver {

    /**
     * 增加一个语言文件
     *
     * @access public
     * @param string $fileName 语言文件名
     * @return void
     */
    abstract public function load($file, $range = '');

    /**
     * Translates a string
     *
     * @access public
     * @param string string to be translated
     * @return string translated string (or original, if not found)
     */
    abstract public function get($string = null, $range = '');

    /**
     * Plural version of gettext
     *
     * @access public
     * @param string single
     * @param string plural
     * @param string number
     * @return translated plural form
     */
    //abstract public function ngettext($single, $plural, $number);

}