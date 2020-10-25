<?php
/**
 * Created by PhpStorm.
 * User: crazycooler
 * Date: 2017/3/25
 * Time: 20:34
 */

namespace App\Common;


class StFetch
{
    public static function fetch($obj,$keys,$ex = null)
    {
        $arr = [];
        foreach($keys as $key){
            $arr[$key] = $obj[$key];
        }
        if($ex){
            $arr = array_merge($arr,$ex);
        }
        return $arr;
    }

    public static function toArray($obj)
    {
        $arr = [];
        foreach($obj as $key => $value){
            $arr[$key] = $value;
        }
        return $arr;
    }

    public static function merge($arr1,$arr2)
    {
        return array_merge($arr1,$arr2);
    }
}