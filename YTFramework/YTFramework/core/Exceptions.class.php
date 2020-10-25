<?php

/**
 * =============================================================================
 *  [YTF] (C)2015-2099 Yuantuan Inc.
 *  This content is released under the Apache License, Version 2.0 (the "License");
 *  Licensed    http://www.apache.org/licenses/LICENSE-2.0
 *  Link        http://www.ytframework.cn
 * =============================================================================
 *  @author     Tangqian<tanufo@126.com> 
 *  @version    $Id: Exceptions.class.php 89 2016-04-21 02:53:46Z lixiaomin $
 *  @created    2015-10-10
 *  异常处理
 * =============================================================================                   
 */

namespace core;

class Exceptions
{

    /**
     * 404提示
     */
    public static function show404()
    {
        header('HTTP/1.1 404 Not Found');
        echo '404 Page Not Found <br>The page you requested was not found.';
        exit;
    }

    public static function showError($msg){
        echo $msg;
        exit;
    }

}
