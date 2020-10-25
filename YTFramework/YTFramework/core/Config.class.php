<?php

/**
 * =============================================================================
 *  [YTF] (C)2015-2099 Yuantuan Inc.
 *  This content is released under the Apache License, Version 2.0 (the "License");
 *  Licensed    http://www.apache.org/licenses/LICENSE-2.0
 *  Link        http://www.ytframework.cn
 * =============================================================================
 *  @author     Tangqian<tanufo@126.com> 
 *  @version    $Id: Config.class.php 89 2016-04-21 02:53:46Z lixiaomin $
 *  @created    2015-10-10
 *  配置存取
 * =============================================================================                   
 */

namespace core;

class Config
{

    protected static $setting = [];

    /**
     * 写入配置
     * @param type $key
     * @param type $value
     */
    public static function set($key, $value)
    {
        self::$setting[$key] = $value;
    }

    /**
     * 读取配置
     * @param type $key
     * @return type
     */
    public static function get($key = null)
    {
        if (empty($key)) {
            return self::$setting;
        }
        return isset(self::$setting[$key]) ? self::$setting[$key] : null;
    }

    /**
     * 获取所有配置信息
     * @return type
     */
    public static function show()
    {
        return self::$setting;
    }

}
