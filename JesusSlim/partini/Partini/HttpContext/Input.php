<?php
/**
 * Created by PhpStorm.
 * User: jesusslim
 * Date: 16/8/3
 * Time: 下午5:20
 */

namespace Partini\HttpContext;


class Input
{

    public function url(){
        return $_SERVER['PHP_SELF'];
    }

    public function uri(){
        return $_SERVER['REQUEST_URI'];
    }

    public function uriForRoute(){
        $uri = $this->uri();
        $index = strpos($uri,'?');
        return $index === false ? $uri : substr($uri,0,$index);
    }

    public function host(){
        return $_SERVER['HTTP_HOST'];
    }

    public function method(){
        return $_SERVER['REQUEST_METHOD'];
    }

    public function isMethod(){
        return $_SERVER['REQUEST_METHOD'];
    }

    public function isGet(){
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    public function isPost(){
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    //TODO
    public function header($key = null){
        $headers = http_get_request_headers();
        return is_null($key) ? $headers : $headers[$key];
    }

    //TODO
    public function isUpload(){
    }

    //TODO
    public function isWebSocket(){
    }


    public function ip(){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
        return $ip ? $ip : '0.0.0.0';
    }

    public function cookie($key = null){
        return is_null($key) ? $_COOKIE : $_COOKIE[$key];
    }

    public function form($key = null){
        return is_null($key) ? $_REQUEST : $_REQUEST[$key];
    }

    public function formGet($key = null){
        return is_null($key) ? $_GET : $_GET[$key];
    }

    public function get($key = null){
        return $this->formGet($key);
    }

    public function formPost($key = null){
        return is_null($key) ? $_POST : $_POST[$key];
    }

    public function post($key = null){
        return $this->formPost($key);
    }
}