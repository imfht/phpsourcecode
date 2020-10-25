<?php
/**
 * Config.php
 * @since   2016-08-26
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace PhalApi\Core;

use PhalApi\Core\Exception\PAException;

class Config {

    static private $config = null;

    static public function init(){
        $baseConf = (array)include_once DOCUMENT_ROOT . '/PhalApi/Config/config.php';
        self::$config = array_change_key_case($baseConf, CASE_UPPER);
    }

    static public function initModuleConf( $module = '' ){
        if( empty($module) ){
            throw new PAException(T('L_PARAM.L_EMPTY'));
        }
        $moduleConfPath = DOCUMENT_ROOT . DS . $module . DS . 'Config';
        if( file_exists($moduleConfPath) ){
            $dirHandle = opendir($moduleConfPath);
            while ( $file = readdir($dirHandle) ){
                if( $file != '.' && $file != '..' && strpos($file, '.php') !== false ){
                    $moduleConf = (array)include_once $moduleConfPath. DS . $file;
                    $moduleConf = array_change_key_case($moduleConf, CASE_UPPER);;
                    self::$config = array_merge(self::$config, $moduleConf);
                }
            }
        }
    }

    static public function get( $item ){
        if( self::$config === null ){
            self::init();
        }
        $item = strtoupper($item);
        if( !isset(self::$config[$item]) ){
            throw new PAException(T('L_CONF')."[{$item}]".T('L_NOT_EXIST'));
        }
        return self::$config[$item];
    }

    static public function set(){

    }

    static public function tempSet(){

    }

}