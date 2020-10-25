<?php


function getMsg($msg = "未知错误", $url = "", $render = true, $status = 200) {
    $msg = [
        'status'    =>  $status,
        'render'    =>  $render,
        'msg'       =>  $msg,
        'url'       =>  $url,
    ];
    return $msg;
}

function getImgUrl($id = 1){
    $img = \think\Db::table('picture')->where(['id'=>$id])->field('path')->find();
    return $img['path'];
}

/**
 * 获取数据库中的配置列表
 * @return array 配置数组
 */
function config_lists(){

    $data   = db('Config')->where(['state'=>1])->field('type,name,value')->select();
    $config = [];
    if($data && is_array($data)){
        foreach ($data as $value) {
            $config[$value['name']] = parse($value['type'], $value['value']);
        }
    }
    return $config;
}

/**
 * 根据配置类型解析配置
 * @param  integer $type  配置类型
 * @param  string  $value 配置值
 */
function parse($type, $value){
    if(3 == $type){
        $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
        if(strpos($value,':')){
            $value  = [];
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[]   = ['key'=>$k,'value'=>$v];
            }
        }else{
            $value =  $array;
        }
    }
    return $value;
}

/**
 * auth密码加密方式
 * @param $password
 * @return string
 */
function auth_password($password){
    return md5(md5($password).config('auth_key'));
}