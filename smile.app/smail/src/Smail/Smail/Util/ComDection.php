<?php
namespace Smail\Util;

class ComDection
{

    public static function is_cn_code($str)
    {
        if (preg_match("/[\x7f-\xff]/", $str)) {
            return true;
        } else {
            return false;
        }
    }
}