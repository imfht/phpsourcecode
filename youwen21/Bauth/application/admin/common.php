<?php

function is_login()
{
    $user = \think\Session::get('userInfo');
    if (!empty($user) && isset($user['id'])) {
        return $user['id'];
    }
    return 0;
}

function is_admin($uid)
{
    if(in_array($uid, ['1'])){
        return true;
    }
    return false;
}

function controToUpper($controller)
{
    $newController = '';
    if(strpos($controller, '_')){
        $arr = explode('_', $controller);
        $newController = $arr[0];
        for($i=1; $i<count($arr); $i++){
            $newController .= ucfirst($arr[$i]);
        }
    }else{
        $newController = $controller;
    }
    return $newController;
}


 // 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}

function array_merge_recursive_distinct ( array &$array1, array &$array2 )
{
  $merged = $array1;

  foreach ( $array2 as $key => &$value )
  {
    if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
    {
      $merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );
    }
    else
    {
      $merged [$key] = $value;
    }
  }

  return $merged;
}
?>