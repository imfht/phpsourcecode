<?php
/**
 * Created by PhpStorm.
 * User: Happy
 * Date: 2016/1/11 0011
 * Time: 21:11
 * 核心文件载入常用类库
 * 只需载入核心组件即可
 *
 */
header('Content-type: text/html; charset=utf-8');  //utf8编码
//根据自己文件位置定义CORE_PATH
$files =  get_included_files();
if(count($files)==2){
     //定义最基础的三个路径
    define('CORE_PATH',str_replace('\\','/',dirname(__FILE__)));
    define('BASE_PATH',str_replace('\\','/',dirname($files[0])));//app目录路径
    define('ROOT_PATH',dirname(CORE_PATH));//app目录路径
}
else{
    exit('core.php should be the second file include,first is maybe index.php');  //在第二个引入
}

//提供一个demo首页并执行的代码
function init(){
    //将init函数替换
 $files =  get_included_files();

$pre_str=file_get_contents($files[0]);
$replace= file_get_contents(CORE_PATH.'/tpl/index.tpl');
   $after= str_replace('init();',$replace,$pre_str);
    file_put_contents($files[0],$after);
    eval($replace);
}
 //用于升级版本
function upgrade(){

  $update_path='D:/tmp/mobile_mall/core';
   $target_path=CORE_PATH;
    //只定向复制几个文件夹，而不是全部复制
    $up_arr=include(CORE_PATH.'/tpl/upgrade.tpl');
    $up_str='';
    foreach($up_arr as $value){
        $up_str.='<br/>core/'.$value;

      $source=$update_path.'/'.$value;
      $target=$target_path.'/'.$value;
        if(is_dir($source)){ //如果是文件夹
            copy_dir($source,$target);
        }
        else{
          copy($source,$target);
        }
    }
    echo  $up_str;
    echo  '<br/>update success!!<br/>';
}
//加载必要的库文件
function lib($libs){
    static $lib;

    if(is_array($libs)){ //如果是数组遍历
  foreach($libs as $value){
      if($lib[$value]){continue;}
      else{
          include(CORE_PATH.'/lib/'.$value.'.php');
          $lib[$value]=true;//代表已经加载过了
      }
  }
    }
    else{ //加载单个文件
        if($lib[$libs]){return true;}
        $lib[$libs]=true;
        include(CORE_PATH.'/lib/'.$libs.'.php');
    }
}
//自动加载
function auto_load($class){
    $flag=@include(CORE_PATH.'/lib/'.$class.'.php');
    if (!$flag){
        print_stack();
        exit("Class Error::【{$class}】isn't exists!");
    }
    return $flag;
}

//自动加载
if(function_exists('spl_autoload_register')) {  //比如model类就是这样加载的
    spl_autoload_register('auto_load');//当php找不到类文件时会调用Base::autoload()方法
} else {
    function __autoload($class) {
        return auto_load($class);
    }
}



