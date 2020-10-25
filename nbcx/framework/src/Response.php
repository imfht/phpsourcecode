<?php
/*
 * This file is part of the NB Framework package.
 *
 * Copyright (c) 2018 https://nb.cx All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace nb;

/**
 * Response
 *
 * @package nb
 * @link https://nb.cx
 * @since 2.0
 * @author: collin <collin@nb.cx>
 * @date: 2017/12/3
 */
class Response extends Component {

    public static $mimes = [
        'bmp'  => 'image/bmp',
        'ico'  => 'image/x-icon',
        'gif'  => 'image/gif',
        'png'  => 'image/png',
        'bin'  => 'application/octet-stream',
        'css'  => 'text/css',
        'tar'  => 'application/x-tar',
        'ppt'  => 'application/vnd.ms-powerpoint',
        'pdf'  => 'application/pdf',
        'swf'  => 'application/x-shockwave-flash',
        'zip'  => 'application/x-zip-compressed',
        'gzip' => 'application/gzip',
        'woff' => 'application/x-woff',
        'svg'  => 'image/svg+xml',
        'xml'  => 'application/xml,text/xml,application/x-xml',
        'json' => 'application/json,text/x-json,application/jsonrequest,text/json',
        'js'   => 'text/javascript,application/javascript,application/x-javascript',
        'rss'  => 'application/rss+xml',
        'yaml' => 'application/x-yaml,text/yaml',
        'atom' => 'application/atom+xml',
        'text' => 'text/plain',
        'jpg'  => 'image/jpg,image/jpeg,image/pjpeg',
        'csv'  => 'text/csv',
        'html' => 'text/html,application/xhtml+xml,*/*',
    ];

    //use \nb\library\Instance;

    public static function config() {
        if(isset(Config::$o->request)) {
            return Config::$o->request;
        }
        return null;
    }

    public static function header($key, $value=null,$http_response_code=null) {
        self::driver()->header($key, $value, $http_response_code);
    }

    /**
     * 重定向跳转
     * @param $url
     * @param int $http_response_code
     */
    public static function redirect($url='/', $http_response_code=302) {
        self::header('Location',$url,$http_response_code);
        //跳转之后，终止继续运行
        quit();
    }


}