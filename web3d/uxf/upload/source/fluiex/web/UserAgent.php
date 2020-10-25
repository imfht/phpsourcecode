<?php

namespace fluiex\web;

class UserAgent
{

    public static function checkRobot($useragent = '')
    {
        static $kw_spiders = array('bot', 'crawl', 'spider', 'slurp', 'sohu-search', 'lycos', 'robozilla');
        static $kw_browsers = array('msie', 'netscape', 'opera', 'konqueror', 'mozilla');

        $useragent = strtolower(empty($useragent) ? $_SERVER['HTTP_USER_AGENT'] : $useragent);
        if (strpos($useragent, 'http://') === false && dstrpos($useragent, $kw_browsers))
            return false;
        if (dstrpos($useragent, $kw_spiders))
            return true;
        return false;
    }

    public static function checkMobile()
    {
        global $_G;
        $mobile = array();
        static $touchbrowser_list = array('iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini',
            'ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung',
            'palmsource', 'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser',
            'up.link', 'blazer', 'helio', 'hosin', 'huawei', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource',
            'alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone',
            'iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop',
            'benq', 'haier', '^lct', '320x320', '240x320', '176x220', 'windows phone');
        static $wmlbrowser_list = array('cect', 'compal', 'ctl', 'lg', 'nec', 'tcl', 'alcatel', 'ericsson', 'bird', 'daxian', 'dbtel', 'eastcom',
            'pantech', 'dopod', 'philips', 'haier', 'konka', 'kejian', 'lenovo', 'benq', 'mot', 'soutec', 'nokia', 'sagem', 'sgh',
            'sed', 'capitel', 'panasonic', 'sonyericsson', 'sharp', 'amoi', 'panda', 'zte');

        static $pad_list = array('ipad');

        $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);

        if (dstrpos($useragent, $pad_list)) {
            return false;
        }
        if (($v = dstrpos($useragent, $touchbrowser_list, true))) {
            $_G['mobile'] = $v;
            return '2';
        }
        if (($v = dstrpos($useragent, $wmlbrowser_list))) {
            $_G['mobile'] = $v;
            return '3'; //wml版
        }
        $brower = array('mozilla', 'chrome', 'safari', 'opera', 'm3gate', 'winwap', 'openwave', 'myop');
        if (dstrpos($useragent, $brower))
            return false;

        $_G['mobile'] = 'unknown';
        if (isset($_G['mobiletpl'][$_GET['mobile']])) {
            return true;
        } else {
            return false;
        }
    }

}
