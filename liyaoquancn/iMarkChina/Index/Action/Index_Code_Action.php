<?php
/*
*All rights reserved: Yao Quan Li.
*Links:http://www.liyaoquan.cn.
*Links:http://www.imarkchina.cn.
*Date:2014.
*/
error_reporting(E_ALL ^ E_NOTICE);
@include_once __Index__.'/Index/Point/Index_Config_Action.php';
@include_once 'Index_Action_Action.php';
function Mark_404() {
$file = $_SERVER['REQUEST_URI'];
if (is_file(__Index__.$file)) {
   $open = file_get_contents(__Index__.$file);
    echo $open; die;
}else{
    include_once __Index__.'/Public/Resources/404.php';
    exit();
  }
}
function Mark_Search_Search($a,$b){
  global $Mark_Config_Action;
//Time out one min (600);
set_time_limit("600");
//Get the keyword;
$keyword=trim($_POST["keyword"]);
$keyword=htmlspecialchars($keyword);
//Check the key word;
if($keyword==""){
echo"keyword is NULL~!<br/>(关键字不能为空~! )";
exit();//Shutdown the PHP Code;
}
function listFiles($dir,$keyword,&$array){
$dir = __Index__.'/Index/Point/Data/Post/Data';
$handle=opendir($dir);
while(false!==($file=readdir($handle))){
if($file!="."&&$file!=".."){
if(is_dir("$dir/$file")){
listFiles("$dir/$file",$keyword,$array);
}
else{
$data=fread(fopen("$dir/$file","r"),filesize("$dir/$file"));
if(@preg_match("/<body([^>]+)>(.+)</body>/i",$data,$c)){
$body=strip_tags($c["2"]);
}
else{
$body=strip_tags($data);
}
if($file!="Index_Code_Action.php"){ //Don't fine me;
if(preg_match("/$keyword/i",$body)){
if(preg_match('/"title"(.+)"tags"/i',$data,$m)){
$title=$m["1"];
}
else{
$title="Can't find Title (没有标题)";
}
$array[]="$dir/$file $title";
}
}
}
}
}
}
$array=array();
listFiles(".","$keyword",$array);
//This "if" form check $array to NULL;
if ($array != null) {
foreach($array as $value){
//open the $filedir and the $title;
list($filedir,$title)=preg_split("[ ]",$value,"2");
// Delete something I don't need,Start;
$postlist = substr($filedir,0,strlen($filedir)-11);
$filedir = str_replace($postlist, '', $filedir);
$filedir = substr($filedir,0,strlen($filedir)-5);
$title = str_replace('";s:4:', '',$title);
$title = str_replace(';s:', '',$title);
$title = str_replace(':"', '',$title);
 $title = str_replace('0', '',$title);
$title = str_replace('1', '',$title);
$title = str_replace('2', '',$title);
$title = str_replace('3', '',$title);
$title = str_replace('4', '',$title);
$title = str_replace('5', '',$title);
$title = str_replace('6', '',$title);
$title = str_replace('7', '',$title);
 $title = str_replace('8', '',$title);
 $title = str_replace('9', '',$title);
$title = preg_replace("@<script(.*?)</script>@is", "", $title); 
$title  = preg_replace("@<iframe(.*?)</iframe>@is", "", $title); 
$title  = preg_replace("@<style(.*?)</style>@is", "", $title); 
$title  = preg_replace("@<(.*?)>@is", "", $title); 
$title = preg_replace("@<?php (.*?) ?>@is", "",$title);
$title = preg_replace("@(.*?);@is", "",$title);
$title = preg_replace("@$(.*?).@is", "",$title);
$title = htmlspecialchars_decode($title); 
$title = preg_replace("/<(.*?)>/","",$title); 
//Delete something I don't need,End;
//Print
if ($Mark_Config_Action['write'] == 'open') {
 echo $a.'<a href="'.$Mark_Config_Action['level'].'/post-'.$filedir.'.html" target="_blank" title="'.$title.'">'.$title.'</a>'.$b;
}else{
 echo $a.'<a href="'.$Mark_Config_Action['level'].'/?post/'.$filedir.'" target="_blank" title="'.$title.'">'.$title.'</a>'.$b;
}
}
} else {
    echo 'My apologize,I can\'t find something Information~! <br/> (非常抱歉，搜索不到相关信息~! )';
}
}
function Mark_keyword(){
  global $Mark_Config_Action;
  if ($Mark_Config_Action['write'] == 'open') {
    echo $Mark_Config_Action['level'].'/search.html';
  }else{
    echo $Mark_Config_Action['level'].'/?search/';
  }
}
?>