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


function checkSaveName($save_name){
        $result = preg_match ('/^[0-9_a-zA-Z\/\.-]*$/',$save_name);
        if($result){
            return true;
        }else{
            return false;
        }
    }

function getFilesize($num){
    $p = 0;
    $format='bytes';
    if($num>0 && $num<1024){
        $p = 0;
        return number_format($num).' '.$format;
    }
    if($num>=1024 && $num<pow(1024, 2)){
        $p = 1;
        $format = 'KB';
    }
    if ($num>=pow(1024, 2) && $num<pow(1024, 3)) {
        $p = 2;
        $format = 'MB';
    }
    if ($num>=pow(1024, 3) && $num<pow(1024, 4)) {
        $p = 3;
        $format = 'GB';
    }
    if ($num>=pow(1024, 4) && $num<pow(1024, 5)) {
        $p = 3;
        $format = 'TB';
    }
    $num /= pow(1024, $p);
    return number_format($num, 3).' '.$format;
}

function countFiles($dir)
{
    $data['file_count'] = 0;
    $data['dir_count'] = -2;
    $list = scandir($dir);
    foreach ($list as $key => $value) {
        if(is_dir($dir.$value)){
            $data['dir_count']++;
        }
        if(is_file($dir.$value)){
            $data['file_count']++;
        }
    }
    $data['count'] = $data['file_count']+$data['dir_count'];
    return $data;

}

function read_all ($dir){
    if(!is_dir($dir)) return false;
    
    $data['dir_count'] = 0;
    $data['file_count'] = 0;
    $handle = opendir($dir);

    if($handle){
        while(($fl = readdir($handle)) !== false){
            $temp = $dir.DIRECTORY_SEPARATOR.$fl;
            //如果不加  $fl!='.' && $fl != '..'  则会造成把$dir的父级目录也读取出来
            if(is_dir($temp) && $fl!='.' && $fl != '..'){
                $data['dir_count']++;
                $deep_data = read_all($temp);
                $data['dir_count'] += $deep_data['dir_count'];
                $data['file_count'] += $deep_data['file_count'];
            }else{
                if($fl!='.' && $fl != '..'){

                    $data['file_count']++;
                }
            }
        }
    }
    $data['count'] = $data['file_count']+$data['dir_count'];
    return $data;
}

function web_url($path)
{
    if(preg_match('/^\..*/',$path)){
        $path = str_replace('./','/',$path);
    }
    return config('host_name').$path;
}

function directory($dir){
    if(is_dir($dir) || @mkdir($dir,0777)){ //查看目录是否已经存在或尝试创建，加一个@抑制符号是因为第一次创建失败，会报一个“父目录不存在”的警告。

        return true;

    }else{

        $dirArr=explode('/',$dir); //当子目录没创建成功时，试图创建父目录，用explode()函数以'/'分隔符切割成一个数组
        array_pop($dirArr); //将数组中的最后一项（即子目录）弹出来，
        $newDir=implode('/',$dirArr); //重新组合成一个文件夹字符串
        if(Directory($newDir)){
            if(@mkdir($dir,0777)){
                return true;

            } 
        }else{
            return false;
        }
    }
    return false;
}


function delTree($dir) {
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
} 