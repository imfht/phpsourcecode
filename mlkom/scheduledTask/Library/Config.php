<?php

/**
 * Created by PhpStorm.
 *
 * @author: moxiaobai
 * @since : 2015/5/12 14:35
 */

namespace Library;

class Config {

    /**
     * 解析配置文件
     *
     * @param $file
     * @param string $env
     * @return mixed
     */
    public static function ini($file, $env='Product') {
        if(!is_file($file)) {
            throw new \Exception("配置文件：{$file} 不存在");
        }

        $iniArray = parse_ini_file($file, true);

        return $iniArray[$env];
    }
}