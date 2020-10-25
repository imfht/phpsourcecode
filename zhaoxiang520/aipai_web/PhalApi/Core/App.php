<?php
/**
 * 这是一个初始化的类，这里解析了URL以及完成了类和函数的调配
 * @since   2016-08-29
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace PhalApi\Core;


use PhalApi\Core\Exception\PAException;

class App {

    public static function run(){
        $module = '\\'.URL::$module.'\\Api\\'.URL::$class;
        try {
            $reflection = new \ReflectionClass($module);
            if( !$reflection->hasMethod(URL::$action) ){
                throw new PAException('Not Exists: Module: ' .URL::$module .', Action: ' .URL::$action);
            }
            $method = $reflection->getMethod(URL::$action);
            $handle = $reflection->newInstance();
            $ret = $method->invokeArgs($handle, []);
            Response::output( $ret );
        }catch (\Exception $e){
            throw new PAException($e->getMessage());
        }

    }

}