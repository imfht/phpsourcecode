<?php
/* Copyright 2015  JefferyWang  (email: admin@wangjunfeng.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function jw_wlu_detect_webbrowser() {
    global $useragent;

    if (preg_match('/baidubrowser/i', $useragent)) {
        $title = "百度浏览器";
        $class = "ua_ucweb";
    } elseif (preg_match('/Firefox/i', $useragent)) {
        $title = '火狐浏览器';
        $class = "ua_firefox";
    } elseif (preg_match('/Maxthon/i', $useragent)) {
        $title = '傲游浏览器';
        $class = "ua_maxthon";
    } elseif (preg_match('/UBrowser/i', $useragent)) {
        $title = 'UC浏览器';
        $class = "ua_ucweb";
    } elseif (preg_match('/UCWEB/i', $useragent)) {
        $title = 'UC浏览器';
        $class = "ua_ucweb";
    } elseif (preg_match('/UCBrowser/i', $useragent)) {
        $title = 'UC浏览器';
        $class = "ua_ucweb";
    } elseif (preg_match('/MetaSr/i', $useragent)) {
        $title = '搜狗浏览器';
        $class = "ua_sogou";
    } elseif (preg_match('/2345Explorer/i', $useragent)) {
        $title = '2345浏览器';
        $class = "ua_2345explorer";
    } elseif (preg_match('/ua_2345chrome/i', $useragent)) {
        $title = '2345加速浏览器';
        $class = "ua_2345chrome";
    } elseif (preg_match('/LBBROWSER/i', $useragent)) {
        $title = '猎豹安全浏览器';
        $class = "ua_lbbrowser";
    } elseif (preg_match('/MicroMessenger/i', $useragent)) {
        $title = '微信内置浏览器';
        $class = "ua_qq";
        $icon = "weixin";
    } elseif (preg_match('/QQ/i', $useragent)) {
        $title = 'QQ浏览器';
        $class = "ua_qq";
    } elseif (preg_match('/MiuiBrowser/i', $useragent)) {
        $title = 'MIUI浏览器';
        $class = "ua_mi";
    } elseif (preg_match('/Chrome/i', $useragent)) {
        $title = 'Chrome浏览器';
        $class = "ua_chrome";
    } elseif (preg_match('/safari/i', $useragent)) {
        $title = 'Safari浏览器';
        $class = "ua_apple";
    } elseif (preg_match('/Opera/i', $useragent)) {
        $title = 'Opera浏览器';
        $class = "ua_opera";
    } elseif (preg_match('/Trident/i', $useragent)) {
        $title = 'Internet Explorer 11';
        $class = "ua_ie";
    } elseif (preg_match('/MSIE/i', $useragent)) {
        $title = 'Internet Explorer';
        $class = "ua_ie";
    } else {
        $title = '其它浏览器';
        $class = "ua_other";
    }

    $icon = isset($icon) ? $icon : 'globe';

    return '<span class="ua"><span class="' . $class . '"><i class="fa fa-' . $icon . '"></i> ' . $title . '</span></span>';
}
