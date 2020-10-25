<?php
use think\facade\Cache;
use app\model\SystemConfig;
// 应用公共文件
function json_message($data = [],$code = 0,$msg = '')
{
    if(is_string($data)){
        
        $code = $code === 0 ? 500 : $code;
        $msg = $data;
        $data = [];
    }

    return json([
        'code'=>$code,
        'msg'=>$msg,
        'data'=>$data
    ]);
}



function get_system_config($name = '',$default = '')
{
    $list = Cache::get('system_config');

    if(empty($list)){
        $list = SystemConfig::column('value','name');
    }

    if($name === ''){
        return $list;
    }

    if(isset($list[$name])){
        return $list[$name];
    }

    return $default;
}


/**
 * 检查端口是否可以被绑定
 * @author flynetcn
 */
function check_port_bindable($listen_address, &$errno=null, &$errstr=null)
{
  
  return true;
}


