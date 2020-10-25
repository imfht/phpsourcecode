<?php

/**
 * AutoLoad.php
 * @since   2016-08-26
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */
namespace PhalApi\Core;

class AutoLoad {

    public static function register(){
        spl_autoload_register(['self','load']);
    }

    private static function load( $className ){
        $class_file =  DOCUMENT_ROOT.'/' .str_replace('\\', '/', $className) .'.php';
        if( file_exists($class_file) ){
            require_once $class_file;
        }else{
            throw new \Exception($className .' 类文件不存在!');
        }
    }

}