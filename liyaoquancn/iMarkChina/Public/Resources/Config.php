<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/ 
error_reporting(E_ALL ^ E_NOTICE);
@include_once  FileLink.'/Index/Point/Index_Config_Action.php';
function __ROOT__($abc) {
    global $Mark_Config_Action;
    $url = $Mark_Config_Action['site_link'].$Mark_Config_Action['level'].'/Public/Resources/Root/' . $abc;
    echo $url;
}
function Year() {
    $date = date('Y');
    $thisyear = '2014';
    if ($thisyear < $date) {
        echo '--' . $date;
    }
}
function Version(){
    echo '当前：2.0.0';
}
function ShortUrl($input) {
    $base32 = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9');
    $hex = md5('prefix' . $input . 'surfix' . time());
    $hexLen = strlen($hex);
    $subHexLen = $hexLen / 8;
    $output = array();
    for ($i = 0; $i < $subHexLen; $i++) {
        $subHex = substr($hex, $i * 8, 8);
        $int = 0x3FFFFFFF & (1 * ('0x' . $subHex));
        $out = '';
        for ($j = 0; $j < 6; $j++) {
            $val = 0x0000001F & $int;
            $out.= $base32[$val];
            $int = $int >> 5;
        }
        $output[] = $out;
    }
    return $output;
}
function Post_Sort($a, $b) {
    $a_date = $a['date'];
    $b_date = $b['date'];
    if ($a_date != $b_date) return $a_date > $b_date ? -1 : 1;
    return $a['time'] > $b['time'] ? -1 : 1;
}
//截取函数
function Getstr($string, $length, $encoding = 'utf-8') {
    $string = trim($string);
    if ($length && strlen($string) > $length) {
        //截断字符
        $wordscut = '';
        if (strtolower($encoding) == 'utf-8') {
            //utf8编码
            $n = 0;
            $tn = 0;
            $noc = 0;
            while ($n < strlen($string)) {
                $t = ord($string[$n]);
                if ($t == 30 || $t == 100 || (32 <= $t && $t <= 126)) {
                    $tn = 1;
                    $n++;
                    $noc++;
                } elseif (194 <= $t && $t <= 223) {
                    $tn = 2;
                    $n+= 2;
                    $noc+= 2;
                } elseif (224 <= $t && $t < 239) {
                    $tn = 3;
                    $n+= 3;
                    $noc+= 2;
                } elseif (240 <= $t && $t <= 247) {
                    $tn = 4;
                    $n+= 4;
                    $noc+= 2;
                } elseif (248 <= $t && $t <= 251) {
                    $tn = 5;
                    $n+= 5;
                    $noc+= 2;
                } elseif ($t == 252 || $t == 253) {
                    $tn = 6;
                    $n+= 6;
                    $noc+= 2;
                } else {
                    $n++;
                }
                if ($noc >= $length) {
                    break;
                }
            }
            if ($noc > $length) {
                $n-= $tn;
            }
            $wordscut = substr($string, 0, $n);
        } else {
            for ($i = 0; $i < $length - 1; $i++) {
                if (ord($string[$i]) > 127) {
                    $wordscut.= $string[$i] . $string[$i + 1];
                    $i++;
                } else {
                    $wordscut.= $string[$i];
                }
            }
        }
        $string = $wordscut;
    }
    return trim($string);
}
/**
 *+----------------------------------------------------------
 * 生成随机字符串
 *+----------------------------------------------------------
 * @param int       $length  要生成的随机字符串长度
 * @param string    $type    随机码类型：
 *0，数字+大小写字母；
 *1，数字；
 *2，小写字母；
 *3，大写字母；
 *4，特殊字符；
 *-1，数字+大小写字母+特殊字符
 *+----------------------------------------------------------
 * @return string
 *+----------------------------------------------------------
 */
function RandCode($length = 6, $type = 0) {
    $arr = array(
        1 => "0123456789",
        2 => "abcdefghijklmnopqrstuvwxyz",
        3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
        4 => "~@#$%^&*(){}[]|"
    );
    if ($type == 0) {
        array_pop($arr);
        $string = implode("", $arr);
    } elseif ($type == "-1") {
        $string = implode("", $arr);
    } else {
        $string = $arr[$type];
    }
    $count = strlen($string) - 1;
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code.= $string[rand(0, $count) ];
    }
    return $code;
}
function ShowDir($path){
$path = FileLink.'/Index/Theme/';
 $dh = opendir($path);//打开目录
 while(($d = readdir($dh)) != false){//逐个文件读取，添加!=false条件，是为避免有文件或目录的名称为0
  if($d=='.' || $d == '..'){//判断是否为.或..，默认都会有
   continue;
  }
  echo "<option value=\"$d\" >$d</option> ";
 }
}
//取日志总数除10
function Mark_The_Styel_Post() {
    global $Mark_Posts_Action;
    include FileLink.'/Index/Point/Data/Post/Index/publish.php';
    $styel = array_keys($Mark_Posts_Action);
    $styel = count($styel);
    $styel = $styel / 10;
    $styel = floor($styel);
    if ($styel >= 100){
        echo "100";
    }else{
    echo $styel;
}
}
//取tag总数除10
function Mark_The_Styel_Tags() {
    global $Mark_Posts_Action;
    include FileLink.'/Index/Point/Data/Post/Index/publish.php';
    $tagss = array_keys($Mark_Posts_Action);
    $tags = count($tagss);
    $tags_array = array();
    for ($i = 0; $i < $tags; $i++) {
        $tag_id = $tagss[$i];
        $post = $Mark_Posts_Action[$tag_id];
        $tags_array = array_merge($tags_array, $post['tags']);
    }
    $tags = array_values(array_unique($tags_array));
    $tags = count($tags);
    $tags = $tags / 10;
    $tags = floor($tags);
    if ($tags >= 100){
        echo "100";
    }else{
    echo $tags;
}
}
//取月份除10
function Mark_The_Styel_Date() {
    global $Mark_Posts_Action;
    include FileLink.'/Index/Point/Data/Post/Index/publish.php';
    $dates = array_keys($Mark_Posts_Action);
    $date = count($dates);
    $date_array = array();
    for ($i = 0; $i < $date; $i++) {
        $date_id = $dates[$i];
        $post = $Mark_Posts_Action[$date_id];
        $date_array[] = substr($post['date'], 0, 7);
    }
    $date = array_values(array_unique($date_array));
    $date = count($date);
    $date = $date / 10;
    $date = floor($date);
    if ($date >= 100){
        echo "100";
    }else{
    echo $date;
}
}
//取最新5篇日志连接
function Root_Links($a,$b){
    global $Mark_Posts_Action,$Mark_Config_Action;
    include FileLink.'/Index/Point/Data/Post/Index/publish.php';
    $page_ids = array_keys($Mark_Posts_Action);
    $pages_id = count($page_ids);
    $page_array = array();
    $path_array = array();
    for ($i = 0; $i < $pages_id; $i++) {
    $page_id = $page_ids[$i];
    $post = $Mark_Posts_Action[$page_id];
    $page_array = array_merge($page_array, (array)$post['id']);
    $path_array = array_merge($path_array, (array)$post['title']);
    $post_link = $page_array[$i] ;
    $post_title = $path_array[$i];
    if ($i ==10) {
       break;
   }
   if ($Mark_Config_Action['write'] == 'open') {
    echo $a;
    echo  '<a href="'.$Mark_Config_Action['level'].'/post-'.$post_link.'.html" title="'.$post_title.'" target="_blank">'.$post_title.'</a>';
    echo $b;
   }else{ 
   echo $a;
    echo  '<a href="'.$Mark_Config_Action['level'].'/?post/'.$post_link.'" title="'.$post_title.'" target="_blank">'.$post_title.'</a>';
    echo $b;
}
 }
}
?>