<?php

/**
 * =============================================================================
 *  [YTF] (C)2015-2099 Yuantuan Inc.
 *  This content is released under the Apache License, Version 2.0 (the "License");
 *  Licensed    http://www.apache.org/licenses/LICENSE-2.0
 *  Link        http://www.ytframework.cn
 * =============================================================================
 *  @author     Tangqian<tanufo@126.com> 
 *  @version    $Id: Autoloader.class.php 89 2016-04-21 02:53:46Z lixiaomin $
 *  @created    2015-10-10
 *  自动引入文件
 * =============================================================================                   
 */

namespace core;

class Autoloader
{

    /**
     * 路径数组 保持唯一
     * @var type 
     */
    private static $file_array = [];

    public static function init($class_name)
    {
        $file = ROOT . DS . str_replace('\\', DS, $class_name) . '.class.php';
        /**
         * 作hash数组 存在直接返回 保证引入的文件唯一
         */
        $hash = md5($file);
        if (isset(self::$file_array[$hash])) {
            return;
        }
        if (file_exists($file)) {
            self::$file_array[$hash] = $file;
            require $file;
        } else {
            Log::set('File ' . str_replace('\\', DS, $class_name) . '.class.php. does not exist.');
        }
    }

}
