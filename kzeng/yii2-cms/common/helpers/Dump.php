<?php
namespace common\helpers;


/**
 * 格式化打印数据
 *
 * @author Tommy <447569003@qq.com>
 */
class Dump
{

    public static function dump($array)
    {
        echo '************** 我要打印啦：*************<br>';
        echo '<pre>';
        print_r($array);
        echo '</pre>';
        echo '************** 打印结束！*************';
    }


}
