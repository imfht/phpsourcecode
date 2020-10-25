<?php

/**
 * =============================================================================
 *  [YTF] (C)2015-2099 Yuantuan Inc.
 *  This content is released under the Apache License, Version 2.0 (the "License");
 *  Licensed    http://www.apache.org/licenses/LICENSE-2.0
 *  Link        http://www.ytframework.cn
 * =============================================================================
 * @author     Tangqian<tanufo@126.com>
 * @version    $Id: Debug.class.php 89 2016-04-21 02:53:46Z lixiaomin $
 * @created    2015-10-10
 *  Debug
 * =============================================================================
 */

namespace core;

class Debug
{

    //错误记录
    protected static $loginfo = [];

    /**
     * 错误提示
     * @return type
     */
    public static function systemErrorHandler()
    {
        if (Config::get('debug')) {
            $message = '';
            if ($e = error_get_last()) {
                $separator = "\r\n";
                $message .= "错误信息:" . $e['message'] . $separator;
                $message .= "出错文件:" . $e['file'] . $separator;
                $message .= "出错行数:" . $e['line'] . $separator;
                $title = 'YTF致命错误信息友情提示';
                Log::set($message);
                $message = str_replace($separator, '<br />', $message);
                $color = '#9C191F';
            } else {
                if (empty(self::$loginfo)) {
                    return;
                }
                foreach (self::$loginfo['message'] as $k => $v) {
                    $message .= '<p>Error:' . $v . '<br>';
                    $message .= 'File:' . self::$loginfo['errfile'][$k] . ' Line: ' . self::$loginfo['errline'][$k] . '</p>';
                }
                $title = 'YTF非致命错误信息友情提示';
                $color = '#337AB7';
            }
            if (!empty($message)) {
                $tpl = '<p></p><div style="display:inline-block;font-family: 微软雅黑;line-height: 1.2em;;border:1px solid #E8E8E8;padding:10px">';
                $tpl .= '<h3 style="font-weight:100;margin:0;margin-bottom:20px;font-size: 16px;height:40px;line-height:40px;padding-left:20px;color:white;background-color:' . $color . ';">' . $title . '</h3>';
                $tpl .= '<div style="padding-left:10px;font-size:14px;">' . $message . '</div>';
                $tpl .= '</div>';
            }
            echo $tpl;
        }
    }

    /**
     * 非致命性错误捕捉
     * @param type $errno
     * @param type $errstr
     * @param type $errfile
     * @param type $errline
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (Config::get('debug')) {
            self::$loginfo['message'][] = $errstr;
            self::$loginfo['errfile'][] = $errfile;
            self::$loginfo['errline'][] = $errline;
        }
    }

}
