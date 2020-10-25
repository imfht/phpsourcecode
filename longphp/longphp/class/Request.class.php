<?php
if(!defined('DIR')){
	exit('Please correct access URL.');
}

class Request {
    public static function __data($data){
        if(is_array($data)){
            foreach($data as $k => $v){
                $data[$k] = addslashes($v);
            }
            return $data;
        }else {
            return addslashes($data);
        }
    }

    public static function __int($data){
        if(is_array($data)){
            foreach($data as $k => $v){
                $data[$k] = (int)$v;
            }
            return $data;
        }else {
            return (int)$data;
        }
    }

    public static function __email($data){
        $preg = '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';
        if(is_array($data)){
            foreach($data as $k => $v)
            if(preg_match($preg, $data[$k])){
                $data[$k] = $v;
            }else {
                $data[$k] = NULL;
            }
            return $data;
        }else {
            if(preg_match($preg, $data)){
                return $data;
            }else {
                return NULL;
            }
        }
    }

    public static function __phone($data){
        $preg = '/1[3,4,5,7,8]{1}[0-9]{9}/';
        if(is_array($data)){
            foreach($data as $k => $v)
            if(preg_match($preg, $data[$k])){
                $data[$k] = $v;
            }else {
                $data[$k] = NULL;
            }
            return $data;
        }else {
            if(preg_match($preg, $data)){
                return $data;
            }else {
                return NULL;
            }
        }
    }

    public static function post($parameter){
        if(empty($_POST[$parameter])){
            return NULL;
        }else {
            return self::__data($_POST[$parameter]);
        }
    }

    public static function get($parameter){
        if(empty($_GET[$parameter])){
            return NULL;
        }else {
            return self::__data($_GET[$parameter]);
        }
    }

    public static function post_int($parameter){
        if(!isset($_POST[$parameter])){
            return 0;
        }else {
            return self::__int($_POST[$parameter]);
        }
    }

    public static function get_int($parameter){
        if(!isset($_GET[$parameter])){
            return 0;
        }else {
            return self::__int($_GET[$parameter]);
        }
    }

    public static function post_email($parameter){
        if(empty($_POST[$parameter])){
            return NULL;
        }else {
            return self::__email($_POST[$parameter]);
        }
    }

    public static function get_email($parameter){
        if(empty($_GET[$parameter])){
            return NULL;
        }else {
            return self::__email($_GET[$parameter]);
        }
    }

    public static function post_phone($parameter){
        if(mb_strlen($_POST[$parameter], 'UTF8') != 11){
            return NULL;
        }

        if(empty($_POST[$parameter])){
            return NULL;
        }else {
            return self::__phone($_POST[$parameter]);
        }
    }

    public static function get_phone($parameter){
        if(mb_strlen($_GET[$parameter], 'UTF8') != 11){
            return NULL;
        }

        if(empty($_GET[$parameter])){
            return NULL;
        }else {
            return self::__phone($_GET[$parameter]);
        }
    }
}
