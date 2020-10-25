<?php
 
class fzz_cache{
public $limit_time = 1000;
public $cache_dir = CACHE_DIR;
function __set($key ,$val){
$this->set($key ,$val);
}
function set($key ,$val,$limit_time=null){
$limit_time = $limit_time ?$limit_time : $this->limit_time;
if(is_dir($this->cache_dir)){
$file = $this->cache_dir."/".$key.".php";
$val = serialize($val);
@file_put_contents($file,$val) or $this->error(__line__,"文件写入失败");
@touch($file,time()+$limit_time) or $this->error(__line__,"更改文件时间失败");
}
}
function __get($key){
return $this->get($key);
}
function get($key){
$file = $this->cache_dir."/".$key.".php";
if (@filemtime($file)>=time()){
return unserialize(file_get_contents($file));
}
else{
@unlink($file);
}
}
function __unset($key){
return $this->_unset($key);
}
function _unset($key){
if (@unlink($this->cache_dir."/".$key.".php")){
return true;
}
else{
return false;
}
}
function __isset($key){
return $this->_isset($key);
}
function _isset($key){
$file = $this->cache_dir."/".$key.".php";
if (@filemtime($file)>=time()){
return true;
}else{
@unlink($file);
return false;
}
}
function clear(){
$files = scandir($this->cache_dir);
foreach ($files as $val){
if (filemtime($this->cache_dir."/".$val)){
@unlink($this->cache_dir."/".$val);
}
}
}
function clear_all(){
$files = scandir($this->cache_dir);
foreach ($files as $val){
@unlink($this->cache_dir."/".$val);
}
}
function error($line,$msg){
die("出错文件：".__file__."\n出错行：$line\n错误信息：$msg");
}
}

?>