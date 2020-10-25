<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

error_reporting(E_ERROR | E_PARSE );

function encryPassword($password, $salt){
    return md5(md5($password . $salt));
}

function getSetting($name){
    $key = 'jtimer_setting_'.$name;
    $value = cache($key);

    if($value){
        return $value;
    }

    $value = \think\Db::name('setting')->where('name',$name)->value('value');
    cache($key,$value);
    return $value;
}