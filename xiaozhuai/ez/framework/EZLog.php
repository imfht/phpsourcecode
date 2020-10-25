<?php

/**
 * Created by PhpStorm.
 * User: xiaozhuai
 * Date: 16/12/19
 * Time: 下午12:06
 */
class EZLog
{

    public static function debug($format, $args=null, $_=null){
        self::log("black", $format, $args, $_);
    }

    public static function info($format, $args=null, $_=null){
        self::log("green", $format, $args, $_);
    }

    public static function warnning($format, $args=null, $_=null){
        self::log("orange", $format, $args, $_);
    }

    public static function err($format, $args=null, $_=null){
        self::log("red", $format, $args, $_);
    }

    private static function log($color, $format, $args=null, $_=null){
        $msg = sprintf($format, $args, $_);
        echo '<font color="' . $color . '">' . $msg . '</font><br>';
    }

}