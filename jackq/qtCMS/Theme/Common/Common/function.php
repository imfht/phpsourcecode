<?php
/*
------------------------------------------------------
参数：
$str_cut    需要截断的字符串
$length     允许字符串显示的最大长度
程序功能：截取全角和半角（汉字和英文）混合的字符串以避免乱码
------------------------------------------------------
*/
function substr_cut($str, $length, $start=FALSE)
{
    if( ! $length){
        return false;
    }
    $str = strip_tags($str);
    $strlen = strlen($str);
    $content = '';
    $sing = 0;
    $count = 0;

    if($length > $strlen) {
        $length = $strlen;
    }
    if($start >= $strlen) {
        return false;
    }

    while($length != ($count-$start))
    {
        if(ord($str[$sing]) > 0xa0) {
            if(!$start || $start <= $count) {
                $content .= $str[$sing].$str[$sing+1].$str[$sing+2];
            }
            $sing += 3;
            $count++;
        }else{
            if(!$start || $start <= $count) {
                $content .= $str[$sing];
            }
            $sing++;
            $count++;
        }
    }
    return $content;
}

function simple_substr($str,$length,$allow_length=54,$strip_tag=false,$start=FALSE){

    if($strip_tag){
        $str = strip_tags($str);
    }
    $strlen = strlen($str);
    $content = '';
    if($strlen < $allow_length) {
        return $str;
    }
    $count = 0;
    $sing = 0;
    while(($length-3) != $count)
    {
        if(ord($str[$sing]) > 0xa0) {
            if(!$start || $start <= $count) {
                $content .= $str[$sing].$str[$sing+1].$str[$sing+2];
            }
            $sing += 3;
            $count++;
        }else{
            if(!$start || $start <= $count) {
                $content .= $str[$sing];
            }
            $sing++;
            $count++;
        }
    }
    return $content.'...';
}


