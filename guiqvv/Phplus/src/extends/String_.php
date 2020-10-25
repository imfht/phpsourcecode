<?php
/**
 * Some useful extend tools for PHP
 */
namespace zendforum\Phplus;

/**
 * for String
 */
class String_ {

    /**
     * 随机字符串生成器
     * @param int $length
     * @param bool $includeSpecialChars 是否包含特殊字符
     * @return string
     */
    public static function random ($length = 6, $includeSpecialChars = false) {
        if (!Int_::is_id($length)) $length = 6;

        $chars = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        $specialChars = '!"#$%&`()*+,-./:;<=>?@[\]^_{|}~' . "'";
        if (boolval($includeSpecialChars)) {
            $chars .= $specialChars;
        }
        $charsIndexMax = strlen($chars) - 1;

        $output = '';
        for ($i = 0; $i < $length; $i++) {
            $output .= $chars{mt_rand(0, $charsIndexMax)};
        }

        return $output;
    }

}
