<?php
/*
 *  2017年3月8日 星期三
 *  家谱系统全局变量
*/
namespace app\Server;
use hyang\Logic;
use think\Db;
class Clan extends Logic
{
    public function __get($key)
    {
        $className = '\\app\Server\\Clan\\'.$key;
        if(class_exists($className)) return new $className();
    }
}