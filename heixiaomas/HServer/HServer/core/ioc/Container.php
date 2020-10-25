<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/7/30
 * Time: 17:43
 */

namespace HServer\core\ioc;
/**
 * Class Ioc
 * @package HServer\core
 * IOC容器
 */
class Container
{
    private static $ioc = [];
    /**
     * @param $name
     * @param $bean
     * @return bool
     */
    public static function addBean($name, $bean)
    {
        if (!empty($name) && !empty($bean)) {
            Container::$ioc[$name] = $bean;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public static function exist($name)
    {

        if (!empty(Container::$ioc[$name])) {
            return true;
        } else {
            return false;
        }

    }

    public static function getBean($name)
    {
        if (!empty(Container::$ioc[$name])) {
            return Container::$ioc[$name];
        } else {
            return null;
        }
    }

    public static function remove($name)
    {
        if (!empty(Container::$ioc[$name])) {
            unset(Container::$ioc[$name]);
            return true;
        } else {
            return false;
        }
    }
}