<?php
/**
 * Created by PhpStorm.
 * @author Luficer.p <81434146@qq.com>
 * Date: 16/11/4
 * Time: 下午4:28
 */

namespace LuciferP\TinyMvc\base;


use LuciferP\Base\ApplicationRegistry;
use LuciferP\Orm\base\Factory;

/**
 * 全局工厂
 * Class TinyMvc
 * @package LuciferP\TinyMvc\base
 * @author Luficer.p <81434146@qq.com>
 */
class TinyMvc
{
    public static function log(){
        return ApplicationRegistry::getValue('log');
    }

    public static function cache(){
        $key = ApplicationRegistry::getConfig()['cache']['driver'];
        $cache = ApplicationRegistry::getValue($key);
        if(!$cache)
        {
            $cache = new $key(BASE_DIR.'/runtime/cache');
            ApplicationRegistry::setValue($key,$cache);
        }
        return $cache;

    }

    public static function config(){
        return ApplicationRegistry::getConfig();
    }

    public static function pdo(){
        return  Factory::getDb('master')->getDb();
    }
}