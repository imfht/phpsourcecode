<?php

/**
 * Created by PhpStorm.
 * User: xiaozhuai
 * Date: 16/12/19
 * Time: 下午4:14
 */
class EZPath
{
    /**
     * remove last '/' if end with '/', if it's '/', just be that
     * @param $var string
     * @param bool $return return value
     * @return mixed|string if $return is true, then return the value
     */
    public static function removeLastSlash(&$var, $return = false){
        $tmp = $var;
        self::normalizeSlash($tmp);
        if($tmp!="/" && substr($tmp, -1) == "/"){
            $tmp = substr($var, 0, -1);
        }
        if(!$return){
            $var = $tmp;
        }else{
            return $tmp;
        }

    }

    /**
     * add a '/' if not end with '/'
     * @param $var string
     * @param bool $return return value
     * @return mixed|string if $return is true, then return the value
     */
    public static function addonLastSlash(&$var, $return = false){
        $tmp = $var;
        self::normalizeSlash($tmp);
        if (substr($tmp, -1) != "/") {
            $tmp = $tmp . "/";
        }
        if(!$return) {
            $var = $tmp;
        }else{
            return $tmp;
        }
    }

    /**
     * replace \ with /
     * @param $var string
     * @param bool $return return value
     * @return mixed|string if $return is true, then return the value
     */
    public static function normalizeSlash(&$var, $return = false){
        $tmp = str_replace("\\", "/", $var);
        self::removeDuplicateSlash($tmp);
        if(!$return){
            $var = $tmp;
        }else{
            return $tmp;
        }
    }

    /**
     * remove duplicate '/'
     * @param $var string
     * @param bool $return return value
     * @return mixed|string if $return is true, then return the value
     */
    public static function removeDuplicateSlash(&$var, $return = false){
        $tmp = preg_replace('/\/+/', '/', $var);
        if(!$return){
            $var = $tmp;
        }else{
            return $tmp;
        }
    }

}