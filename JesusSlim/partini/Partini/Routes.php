<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/7/29
 * Time: 下午4:25
 */

namespace Partini;


class Routes
{

    const FUNC_ALLOWED = array(
        'get','post','put','patch','delete','options','all','group'
    );

    public static function __callStatic($func, ...$args){
        if(!in_array($func,self::FUNC_ALLOWED)){
            throw new \Exception("method $func not found");
        }
        $obj = Application::getInstance()->produce('router');
        return call_user_func_array([$obj,$func],...$args);
    }

}