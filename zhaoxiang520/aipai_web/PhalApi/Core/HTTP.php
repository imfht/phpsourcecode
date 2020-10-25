<?php
/**
 * GET，POST数据处理
 * @since   2016-08-31
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace PhalApi\Core;


use PhalApi\Core\Exception\PAException;

class HTTP {

    private static $get = [];
    private static $post = [];
    private static $put = [];
    private static $delete = [];
    private static $data = [];

    public static $uid = '-';
    public static $userName = '-';
    public static $userAccount = '-';

    public static function init(){
        $type = strtolower($_SERVER['REQUEST_METHOD']);
        switch ( $type ) {
            case 'get':
                self::$get = self::$data = self::prepare($_GET);
                break;
            case 'post':
                self::$post = self::$data = self::prepare($_POST);
                break;
            case 'put':
                parse_str(file_get_contents('php://input'), $put);
                self::$put = self::$data = self::prepare($put);
                break;
            case 'delete':
                parse_str(file_get_contents('php://input'), $delete);
                self::$delete = self::$data = self::prepare($delete);
                break;
        }
    }

    private static function prepare( $request ){
        if( empty($request) ){
            return [];
        }
        if( isset($request['sid']) ){
            $userInfo = Cache::get('sid:'.$request['sid']);
            if( !empty($userInfo) ){
                $userInfoArr = json_decode($userInfo, true);
                self::$uid = $userInfoArr['uid'];
                self::$userName = $userInfoArr['userName'];
                self::$userAccount = $userInfoArr['userAccount'];
            }
        }
        if( isset($request['data']) ){
            if( is_string($request['data']) ){
                $dataArr = json_decode($request['data'], true);
                if( $dataArr === $request['data'] || is_null($dataArr) ){
                    throw new PAException('URL'.T('L_PARAM.L_INVALID'));
                }
            }else{
                $dataArr = $request['data'];
            }
            return $dataArr;
        }else{
            throw new PAException('URL'.T('L_PARAM.L_NOT_EXIST'));
        }
    }

    public static function get( $item ){
        if( isset(self::$get[$item]) ){
            return self::$get[$item];
        }
        return '';
    }

    public static function put( $item ){
        if( isset(self::$put[$item]) ){
            return self::$put[$item];
        }
        return '';
    }

    public static function delete( $item ){
        if( isset(self::$delete[$item]) ){
            return self::$delete[$item];
        }
        return '';
    }

    public static function post( $item ){
        if( isset(self::$post[$item]) ){
            return self::$post[$item];
        }
        return '';
    }

    public static function getAll(){
        if( isset(self::$data) ){
            return self::$data;
        }
        return [];
    }

}