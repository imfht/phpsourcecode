<?php
/**
 * hash 函数工具
 * @package herosphp\utils
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since v1.2.1
 */
namespace herosphp\utils;

class HashUtils {

    /**
     * 采用bkdr算法计算hash值
     * @param string $str
     * @return int
     */
    public static function BKDRHash( $str ) {

        $hval = 0;
        $len  = strlen($str);

        /*
         * 4-bytes integer we will directly take
         * its int value as the final hash value.
        */
        $seed = 131;    // 31 131 1313 13131 131313 etc..
        if ( $len <= 11 && is_numeric($str) ) {
            $hval = intval($str);
        } else {
            for ( $i = 0; $i < $len; $i++ ) {
                $hval = (int) ($hval * $seed + (ord($str[$i]) % 127));
            }
        }

        return ($hval & 0x7FFFFFFF);
    }

    /**
     * JS hash 算法， invented by Justin Sobel
     * @param string $str
     * @return int
     */
    public static function JSHash( $str ) {
        $hcode = 0;
        $len = strlen($str);
        for ( $i = 0; $i < $len; $i++ ) {
            $hcode ^= ( ($hcode << 5) + (ord($str[$i])) + ($hcode << 2) );
        }
        return ($hcode & 0x7FFFFFFF);
    }

    /**
     * DJP hash 算法.
     * invented by doctor Daniel J. Bernstein.
     * @param $str
     * @return int
     */
    public static function DJPHash( $str ) {

        //$hcode = 5381;
        $hcode = 53;
        $len = strlen($str);
        for ( $i = 0; $i < $len; $i++ ) {
            $hcode += ($hcode << 5) + ord($str[$i]);
        }
        return ($hcode & 0x7FFFFFFF);
    }
} 