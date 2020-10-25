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



/**
 * 缩略图生成
 * @param sting $src
 * @param intval $width
 * @param intval $height
 * @param boolean $replace
 * @param intval $type 
                 1、标识缩略图等比例缩放类型
                 2、标识缩略图缩放后填充类型 
                 3、标识缩略图居中裁剪类型
                 4、标识缩略图左上角裁剪类型
                 5、标识缩略图右下角裁剪类型
                 6、标识缩略图固定尺寸缩放类型
 * @return string
 */
function thumb($src = '', $width = 500, $height = 500, $type = 1, $replace = false) {
    $src = './'.$src;
    if(is_file($src) && file_exists($src)) {
        $ext = pathinfo($src, PATHINFO_EXTENSION);
        $name = basename($src, '.'.$ext);
        $dir = dirname($src);
        if(in_array($ext, array('gif','jpg','jpeg','bmp','png'))) {
            $name = $name.'_thumb_'.$width.'_'.$height.'.'.$ext;
            $file = $dir.'/'.$name;
            if(!file_exists($file) || $replace == TRUE) {
                $image = \think\Image::open($src);
                $image->thumb($width, $height, $type);
                $image->save($file);
            }
            $file=str_replace("\\","/",$file);
            $file = '/'.trim($file,'./');
            return $file;
        }
    }
    $src=str_replace("\\","/",$src);
    $src = '/'.trim($src,'./');
    return $src;
}

/**
 * 删除目录（包括下面的文件）
 * @return void
 */
function delDir($directory, $subdir = true) {
    if (is_dir($directory) == false) {
        return false;
    }
    $handle = opendir($directory);
    while (($file = readdir($handle)) !== false) {
        if ($file != "." && $file != "..") {
            is_dir("$directory/$file") ?delDir("$directory/$file") : unlink("$directory/$file");
        }
    }
    if (readdir($handle) == false) {
        closedir($handle);
        rmdir($directory);
    }
}

/**
 * 对一个给定的二维数组按照指定的键值进行排序
 * @return array
 */
function array_sort($arr,$keys,$type='asc'){  
    $keysvalue = $new_array = array();  
    foreach ($arr as $k=>$v){  
        $keysvalue[$k] = $v[$keys];  
    }  
    if($type == 'asc'){  
        asort($keysvalue);  
    }else{  
        arsort($keysvalue);  
    }  
    reset($keysvalue);  
    foreach ($keysvalue as $k=>$v){  
        $new_array[$k] = $arr[$k];  
    }  
    return $new_array;  
} 
/**
 * 时间日期格式化为多少天前
 * @param sting|intval $date_time
 * @param intval $type 1、'Y-m-d H:i:s' 2、时间戳
 * @param intval $format 自定义事件格式
 * @return string
 */
function format_datetime($date_time,$type=1,$format=''){
    if($type == 1){
        $timestamp = strtotime($date_time);
    }elseif($type == 2){
        $timestamp = $date_time;
        $date_time = date('Y-m-d H:i:s',$date_time);
    }
    if($format){
        return date($format,$timestamp);
    }
    $difference = time()-$timestamp;
    if($difference <= 180){
        return '刚刚';
    }elseif($difference <= 3600){
        return ceil($difference/60).'分钟前';
    }elseif($difference <= 86400){
        return ceil($difference/3600).'小时前';
    }elseif($difference <= 2592000){
        return ceil($difference/86400).'天前';
    }elseif($difference <= 31536000){
        return ceil($difference/2592000).'个月前';
    }else{
        return ceil($difference/31536000).'年前';
        //return $date_time;
    }
}