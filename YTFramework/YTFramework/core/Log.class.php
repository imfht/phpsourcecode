<?php

/**
 * =============================================================================
 *  [YTF] (C)2015-2099 Yuantuan Inc.
 *  This content is released under the Apache License, Version 2.0 (the "License");
 *  Licensed    http://www.apache.org/licenses/LICENSE-2.0
 *  Link        http://www.ytframework.cn
 * =============================================================================
 *  @author     Tangqian<tanufo@126.com> 
 *  @version    $Id: Log.class.php 89 2016-04-21 02:53:46Z lixiaomin $
 *  @created    2015-10-10
 *  日志处理
 * =============================================================================                   
 */

namespace core;

class Log
{

    protected static $loginfo = [];

    public static function set($msg = '', $error = false)
    {
        self::$loginfo[] = $msg;
        $fp = fopen(ROOT . DS . 'log' . DS . date('Ymd') . ".log.txt", "a");
        flock($fp, LOCK_EX);
        fwrite($fp, date('Y-m-d H:i:s') . "\t" . $msg . "\r\n");
        flock($fp, LOCK_UN);
        fclose($fp);
        if ($error) {
            die($msg);
        }
    }

    public static function show()
    {
        return self::$loginfo;
    }

}
