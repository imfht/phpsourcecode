<?php
namespace plugins\weixin\admin;

use app\common\controller\admin\Setting AS _Setting;


class Setting extends _Setting
{
    protected function getSysId(){
        $array = plugins_config(config('keywords')); 
        return -$array['id'];   //插件用负数，模块用正数
    }
    
}

