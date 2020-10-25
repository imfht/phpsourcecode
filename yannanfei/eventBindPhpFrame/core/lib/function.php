<?php
/**
 * Created by PhpStorm.
 * User: Happy
 * Date: 2016/1/11 0011
 * Time: 21:09
 */
//获取前端某个资源，比如bootstrap或者jquery等等，存储到本地core/resource文件夹中,更新程序
function upgrade_resource($resource){
    $update_path='D:/tmp/mobile_mall/core/resource/'.$resource;
    if(file_exists($update_path)){
        $target=CORE_PATH.'/resource/'.$resource;
        mk_dir($target);
        copy_dir($update_path,$target);
        echo $resource.' upgrade success!!';
    }
    else{
       echo  $resource.'not exist!!';
    }

}
//加载指定位置配置文件,默认
function load_config($app=''){
if(!$app){$app=basename(BASE_PATH);}
  $path=  ROOT_PATH.'/'.$app.'/config/config.ini.php';
   if(file_exists($path)){
     return include($path);
   }
    else{
        return array();
    }
}
//加载单独的配置，适用于多管理系统
function load_separate_config($app_name){
    $path=ROOT_PATH.'/separate/'.$app_name.'/config/config.ini.php';
    if(file_exists($path)){
        return include($path);
    }
    else{
        return array();
    }
}

//跳转链接
function redirect($url){
    header('Location:'.$url);
}

/**
 * 结束测试，并把结果写入文件中
 * 计算起始时间差
 **/
function  time_end($start_time = 0, $info = "")
{
   $end_arr=explode(' ',microtime(true));
   $start_arr= explode(' ', $start_time);

    $start= (float)sprintf('%.0f',(floatval($start_arr[0])+floatval($start_arr[1]))*1000);
    $end= (float)sprintf('%.0f',(floatval($end_arr[0])+floatval($end_arr[1]))*1000);
    //echo (microtime(true)-$start_time)*1000,'ms';
    $diff = ($end-$start) . 'ms';
    write($info . ' :' . $diff);//写入文件
    return $diff;
}
//判断是否由手机访问
function isMobile(){
    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
    $uachar = "/(mobile|android|iphone)/i";
    if((preg_match($uachar, $ua)))
    {
        return  true;
    }
    else {
        return false;
    }
}
//做加法
function add($add1,$add2){
return $add1+$add2;
}
//统一service函数接口
function  send_data($data=array()){
    exit(json_encode($data));
}
//写入php文件
function write_file($data, $file_path = 'a.php')
{
    if (!is_array($data) && !is_scalar($data)) {
        return false;
    }
    $data = var_export($data, true);
    $data = "<?php return " . $data . ";";
    //写独占锁
    $fp = fopen($file_path , 'w');
    if(flock($fp , LOCK_EX)){
        fwrite($fp ,$data);
        flock($fp , LOCK_UN); //写完解锁
    }
    fclose($fp);
   // return file_put_contents($file_path, $data);
}
//写入文件
function  write($val, $file = 'a.html',$val2='')
{
    if(is_string($file)){
        $file_flag=strstr($file,'.html')||strstr($file,'.log')||strstr($file,'.txt')||strstr($file,'.php');
        if(!$file_flag){
            $val2=$file;//第二个参数也作为变量输出
            $file='a.html';//默认的设置
        }
    }
    else{ //非字符串重置
        $val2=$file;//第二个参数也作为变量输出
        $file='a.html';//默认的设置
    }

    $fh = fopen($file, "a+");
    if (is_array($val)) {
        $val = var_export($val, true);//如果是数组
    }
    if($val2){
       if(is_array($val2)){
           $val.=chr(10).var_export($val2, true);//如果是数组
       }
        else{
            $val.=chr(10).$val2;//如果是数组
        }
    }

    fwrite($fh, chr(10) . $val . '<br/>' . chr(10));    //
    fclose($fh);
}
//打印堆栈信息
function print_stack(){
//header("Content-type: text/html; charset=utf-8");
$array =debug_backtrace();
//print_r($array);//信息很齐全
unset($array[0]);
$html ='';
foreach($array as $row)
{
    $html .=$row['file'].':'.$row['line'].'行,调用方法:'.$row['function']."<p>";
}
echo $html;
}

//获取堆栈信息
function get_stack(){
    $array =debug_backtrace();
//print_r($array);//信息很齐全
    $html ='';
    foreach($array as $row)
    {
        $html .=$row['file'].':'.$row['line'].'行,调用方法:'.$row['function'].chr(10);
    }
    return $html;
}

//递归复制文件夹
function copy_dir($src,$des) {
    $dir = opendir($src);
    @mkdir($des);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                copy_dir($src . '/' . $file,$des . '/' . $file);
            }  else  {
                copy($src . '/' . $file,$des . '/' . $file);
            }
        }
    }
closedir($dir);
}

/**
 * 循环创建目录
 *
 * @param string $dir 待创建的目录
 * @param  $mode 权限
 * @return boolean
 */
function mk_dir($dir, $mode = '0777')
{
    return  is_dir ( $dir ) or mk_dir(dirname( $dir )) and  mkdir ( $dir , 0777);
}

//获取上传文件的路径$use_root_direct代表是否直接使用root作为根目录而不创建年月日的文件夹，实际就是自己指定文件所在的文件夹
function mk_uploads_path($root='',$nameExt='png',&$filename,$use_root_direct=false){

    if(!$root){$root=BASE_PATH;}
    if($use_root_direct){
        $v_path='';
    }else{
        //生成文件夹名和路径
        $v_path = date('Y'); //年
        if(!is_dir($root.'/'.$v_path)) {
            mkdir($root.'/'.$v_path, 0777);
            chmod($root.'/'.$v_path, 0777);
        }
        $v_path .= '/'.date('m'); //月
        if(!is_dir($root.'/'.$v_path)) {
            mkdir($root.'/'.$v_path, 0777);
            chmod($root.'/'.$v_path, 0777);
        }
        $v_path .= '/'.date('d');//日
        if(!is_dir($root.'/'.$v_path)) {
            mkdir($root.'/'.$v_path, 0777);
            chmod($root.'/'.$v_path, 0777);
        }
    }


    $path=$root.'/'.$v_path;
    mk_dir($path);
    $index_file=$path.'/index.php';
    if(!file_exists($index_file)){
        file_put_contents($index_file,''); //放置index文件放置文件以列表展现
    }

    $v_filename =randomString(16, 'abcdefghijklmnopqrstuvwxyz0123456789').'.'.$nameExt;
    while(is_file($path.'/'.$v_filename)) {//如果有重名文件则重新生成随机串值
        $v_filename = randomString(16, 'abcdefghijklmnopqrstuvwxyz0123456789').'.'.$nameExt;
    }
    $filename=$v_path.'/'.$v_filename;
    return $path.'/'.$v_filename;
}

function randomString($length, $source = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') {
    $sourceLength = strlen($source);
    $temp = '';
    for ($i = $length; $i; $i--)
        $temp .= $source[rand(0, $sourceLength - 1)];
    return $temp;
}
//获取数据
function I($name, $type = 'REQUEST'){
     if(isset($_GET[$name])){
         return $_GET[$name];
     }
    elseif(isset($_POST[$name])){
        return $_POST[$name];

    }
    else{
        return '';
    }

}

//根据模板获取相对文件的路径
/* 单独后台系统多模板的情况，需要设置c('template','template')和获取template的文件的情况
module指定使用哪个组的文件，没有则使用设置的模块组
 * **/
function view($tpl){
        $t=c('template');
        if(!$t||$t=='views'){
            return $tpl;
        }
        return 'template/'.$t.'/'.$tpl;

}

//获取真实的ip地址
function get_ip() {
    static $ip = '';
    if($ip){return $ip;}
    $ip = $_SERVER['REMOTE_ADDR'];
    if(isset($_SERVER['HTTP_CDN_SRC_IP'])) {
        $ip = $_SERVER['HTTP_CDN_SRC_IP'];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
        foreach ($matches[0] AS $xip) {
            if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                $ip = $xip;
                break;
            }
        }
    }
    return $ip;
}
//获取设备信息
function get_agent(){
    $user_agent=  $_SERVER['HTTP_USER_AGENT'];
    preg_match('~.+?\((.+?)\).+?~',$user_agent,$match);
    if($match){
        $device=$match[1];
        $wei_flag= strstr($user_agent,'MicroMessenger');
        return $device.' '.$wei_flag;
    }
    else{
        return '未知设备';
    }
}


//生成唯一的随机的id,毫秒记录,减少冲突概率
function get_unique_id() {
    $endtime=1356019200;//2012-12-21时间戳
    $curtime=microtime(true);//当前时间戳
    $newtime=($curtime-$endtime)*1000;//新时间戳
    $rand=rand(0,1000);//两位随机
    $all=$rand.$newtime;
    $onlyid=base_convert($all,10,36);//把10进制转为36进制的唯一ID
    return $onlyid;
}

function getRandOnlyId(){
    return get_unique_id();
}
//设置文件缓存的值  第三个参数为设置过期时间 单位为秒
/*
function set_file_cache($key, $value, $expire = 0,$path='',$ext='.html')
{
    // xdebug_print_function_stack();
    if(!$path){
        $path= BASE_PATH.'/cache';
    }
    if (!is_dir($path)) {
        mkdir($path);
    }
    $cache_path = $path . '/' . $key . $ext;
    if (is_array($value)) {
        $value = serialize($value);
    }

    file_put_contents($cache_path, $value);

    //计算过期时间
    if ($expire != 0) {
        $time_path = $path . '/' . $key . '_time'.$ext;
        $expire_time = time() + $expire; //变成毫秒
        file_put_contents($time_path, $expire_time);// 放置过期时间
    }
    return true;
}

//获取文件缓存的值  获取文件，过期返回false

function  get_file_cache($key,$path='',$ext='.html')
{
    if(!$path){
        $path= BASE_PATH.'/cache';
    }
    $cache_path = $path . '/' . $key . $ext;
    $time_path = $path . '/' . $key . '_time'.$ext;

    if (file_exists($time_path)) {
        $time = file_get_contents($time_path);
        if ($time == 0 || $time > time()) { //如果永不过期或者还没有过期 读取文件
            if (file_exists($cache_path)) {
                $content = file_get_contents($cache_path);
                if (!$content || strlen($content) < 2) {
                    return $content;
                }
                if (substr($content, 0, 2) == 'a:') {//数组需要反序列化
                    return unserialize($content);
                } else {
                    return $content;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    else {
        if (file_exists($cache_path)) {
            $content = file_get_contents($cache_path);
            if (!$content || strlen($content) < 2) {
                return $content;
            }
            if (substr($content, 0, 2) == 'a:') {//数组需要反序列化
                return unserialize($content);
            } else {
                return $content;
            }
        } else {
            return false;
        }
    }
}

//替换css js相对路径到url
 function   replace_resource_url($content,$theme='default'){

     if(substr($theme,0,1)=='@'){ //代表是本地的模板和内容
         $resource_url=substr($theme,1);
     }
    else{
        $resource_url='/core/theme/'.$theme.'/resource';
    }

    $pattern='~(<link.+?href=").+?/resource(.+?>)~';
    $content= preg_replace($pattern,'\\1'. $resource_url.'\\2',$content);
    $pattern='~(<script.+?src=").+?/resource(.+?</script>)~';
    $content= preg_replace($pattern,'\\1'. $resource_url.'\\2',$content);
    //图片资源替换
    $pattern='~(<img.+?src=").+?/resource(.+?/>)~';
    $content= preg_replace($pattern,'\\1'. $resource_url.'\\2',$content);
    //$content=$this->place_holder_replace($content);//替换占位符为具体处理文件
   return  $content;
}*/

