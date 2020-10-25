<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/28
 * Time: 11:39
 */

namespace naples\lib;


use naples\lib\base\Service;

/**
 * 全局配置管理类
 */
class Config extends Service
{
    public function getSetConfig($action='',$value1=FLAG_NOT_SET, $value2=null){
        return \Yuri2::arrGetSet($this->configs,$action,$value1, $value2);
    }
}