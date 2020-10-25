<?php
/**
 * iPHP - i PHP Framework
 * Copyright (c) iiiPHP.com. All rights reserved.
 *
 * @author iPHPDev <master@iiiphp.com>
 * @website http://www.iiiphp.com
 * @license http://www.iiiphp.com/license
 * @version 2.1.0
 */
class iVendor {
    public static $name = null;
    public static $dir = 'src';

    public static function loader($class) {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        $path = str_replace(iVendor::$name . DIRECTORY_SEPARATOR, '', $path);
        $file = iPHP_LIB . '/'.iVendor::$name.'/'.iVendor::$dir.'/' . $path . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
    public static function register($name,$dir='src') {
        iVendor::$name = $name;
        iVendor::$dir  = $dir;
        spl_autoload_register(array('iVendor', 'loader'));
    }
    public static function run($name, $args = null,$self=false) {
        $vendor = '/vendor/Vendor.' . $name . '.php';
        $path = iPHP_APP_LIB.$vendor;
        is_file($path) OR $path = iPHP_LIB.$vendor;

        iPHP::import($path);
        if (function_exists($name)) {
            if($args === null){
                return $name();
            }
            return call_user_func_array($name, (array)$args);
        } else {
            $class_name = 'Vendor_'.$name;
            $flag = class_exists($class_name,false);
            if(!$flag && $self){
                $class_name = $name;
                $flag = class_exists($class_name,false);
            }
            if($flag) {
                if($args === null){
                    return new $class_name;
                }
                if (method_exists($class_name, '__initialize')){
                    return call_user_func_array(array($class_name,'__initialize'), (array)$args);
                }else{
                    return new $class_name($args);
                }
            }else{
                return false;
            }
        }
    }
}
