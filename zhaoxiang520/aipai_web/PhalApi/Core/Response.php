<?php
/**
 * 规范数据输出
 * @since   2016-09-03
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace PhalApi\Core;


use PhalApi\Core\Exception\PAException;
use PhalApi\Core\Response\Json;
use PhalApi\Core\Response\Xml;

class Response {

    private static $handle = null;
    private static $debug = [];
    protected $code = 200;

    private static function init() {
        $type = strtolower(Config::get('RETURN_TYPE'));
        switch ($type){
            case 'json':
                self::$handle = new Json();
                break;
            case 'xml':
                self::$handle = new Xml();
                break;
            default:
                throw new PAException('Response '.T('L_TYPE.L_INVALID'));
                break;
        }
    }

    public static function debug( $debugInfo ){
        if( !empty($debugInfo) ){
            self::$debug[] = $debugInfo;
        }
    }

    public static function output( $data = [] ){
        if( is_null(self::$handle) ){
            self::init();
        }
        if( !empty(self::$debug) ){
            $data['debugInfo'] = self::$debug;
        }
        if( Config::get('DEBUG') ){
            (new Log())->recordSystem();
        }
        (new Log())->recordApi($data);
        self::$handle->create( $data );
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

}