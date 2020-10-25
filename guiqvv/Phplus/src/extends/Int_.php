<?php
/**
 * Some useful extend tools for PHP
 */
namespace zendforum\Phplus;

use zendforum\Phplus\Debug;

/**
 * for Integer
 */
class Int_ {

    /**
     * 判断是否大于0的整形数据
     * @param int $id
     * @return bool
     */
    public static function is_id ($id = 0) {
        if (isset($id) && is_numeric($id) && $id > 0) return true;

        return false;
    }

    /**
     * 获取int型最大值
     * @return int
     */
    public static function max () {
        return PHP_INT_MAX;

        //64位
//        gettype(9223372036854775807);//integer
//        gettype(9223372036854775808);//double
    }

    /**
     * 获取int型的字长,[64位]:8; [32位]:4
     * @return int
     */
    public static function size () {
        return PHP_INT_SIZE;
    }

}
