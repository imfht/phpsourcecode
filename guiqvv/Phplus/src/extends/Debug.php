<?php
/**
 * Some useful extend tools for PHP
 */
namespace zendforum\Phplus;

/**
 * for PHP Debug
 */
class Debug {
    public static function vd (...$params) {
        var_dump(...$params);
        echo self::eol();
    }

    public static function vdd (...$params) {
        var_dump(...$params);
        echo self::eol();
        die;
    }

    public static function pr ($params, $return = false) {
        return print_r($params, $return);
    }

    private static function eol () {
        return (php_sapi_name() === 'cli') ? PHP_EOL : '<br>';
    }

}
