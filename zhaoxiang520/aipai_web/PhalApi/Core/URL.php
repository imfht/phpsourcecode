<?php
/**
 * 用于解析URL
 * 一个完整的URL示例: http://your.domain.com/module/class/action(后面可以接上除了.php以外的任意后缀)
 * 注:每次Api请求,系统只会获取一份数据,也就是数Get和Post数据不可以同时发送
 * 系统权限管理严格按照请求方式,请严格定义你的请求方式
 * @since   2016-08-29
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace PhalApi\Core;


use PhalApi\Core\Exception\PAException;

class URL {

    public static $module;
    public static $class;
    public static $action;

    public static function init(){
        $urlType = Config::get('URL_TYPE');
        switch ($urlType){
            case 0:
                self::pathInfo( self::$module, self::$class, self::$action );
                break;
            case 1:
                break;
            default:
                throw new PAException('url type'.T('L_INVALID'));
                break;
        }
        Config::initModuleConf( self::$module );
    }

    private static function pathInfo( &$module, &$class, &$action ){
        if( !isset($_SERVER['PATH_INFO']) ){
            throw new PAException('PathInfo'.T('L_PARAM.L_NOT_EXIST'));
        }
        $module = '';
        $class = '';
        $action = '';
        if( !empty($_SERVER['PATH_INFO']) ){
            $pathInfoArr = explode( '/', trim($_SERVER['PATH_INFO'] ,'/'));
            switch (count($pathInfoArr)){
                case 3:
                    list( $module, $class, $action ) = $pathInfoArr;
                    break;
                case 2:
                    list( $class, $action ) = $pathInfoArr;
                    break;
                case 1:
                    list( $action ) = $pathInfoArr;
                    break;
            }
        }
        $module = ucfirst(empty($module)?Config::get('DEFAULT_MODULE'):$module);
        $class = ucfirst(empty($class)?Config::get('DEFAULT_CLASS'):$class);
        $action = ucfirst(empty($action)?Config::get('DEFAULT_ACTION'):explode('.', $action)[0]);
    }

    public static function build( $param = '' ){
        $paramArr = explode('/', $param);
        $paramNum = count($paramArr);
        switch ( $paramNum ){
            case 1:
                $uri = self::$module.'/'.self::$class.'/'.$paramArr[0];
                break;
            case 2:
                $uri = self::$module.'/'.$paramArr[0].'/'.$paramArr[1];
                break;
            case 3:
                $uri = $paramArr[0].'/'.$paramArr[1].'/'.$paramArr[2];
                break;
            default:
                throw new PAException('param'.T('L_PARAM.L_INVALID'));
                break;
        }
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
        $url = $protocol.$_SERVER['HTTP_HOST'].'/'.$uri .'.'. Config::get('URL_HTML_SUFFIX');
        return $url;
    }

}