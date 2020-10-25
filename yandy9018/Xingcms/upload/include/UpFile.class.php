<?php

class UpFile {
private $upPath = '';
private $maxSize;
private $allowType = array();
private $saveInfo = array();
private $inputName = '';
private $uploadInfo = array();
private $overWrite = false;
function __construct($upPath = '',$maxSize = 1024,$allowType = '') {
$this ->upPath = ($upPath == '') ?$this ->defaultUpPath() : $this ->pathFormat($upPath);
$this ->maxSize = $maxSize * 1024;
$this ->allowType = ($allowType == '') ?$this ->defaultAllowType() : $allowType;
}
private function defaultUpPath() {
return $this ->pathFormat("upLoad/".date('Y-m') ."/".date('d') .'/');
}
private function defaultAllowType() {
return array('.gif','.jpg','.jpeg','.png');
}
public function setUpPath($path) {
$this ->upPath = $this ->pathFormat($path);
}
public function setMaxSize($maxSize) {
$this ->maxSize = $maxSize * 1024;
}
public function setAllowType($allowType) {
$this ->allowType = $allowType;
}
public function upload($inputName,$saveName = '',$overWrite = false) {
$this ->inputName = $inputName;
$this ->overWrite = $overWrite;
$this ->uploadInfo[$this ->inputName] = $_FILES[$this ->inputName];
$this ->uploadInfo[$this ->inputName]['save_name'] = $saveName;
$this ->validate();
}
private function validate() {
extract($this ->uploadInfo[$this ->inputName]);
$this ->setSaveInfo('error','');
if ($error != 0) {
if ($error == 1 ||$error == 2) $this ->error('大小超出系统允许');
elseif ($error == 3) $this ->error('只有部分被上传');
elseif ($error == 4) $this ->error('没有文件上传');
return false;
}
if (!$this ->chkSize()) {
$this ->error('为空或大小超出允许 '.$this ->byteFormat($this ->maxSize));
return false;
}
if (!$this ->chkType()) {
$this ->error('类型超出允许，允许类型：'.$this ->getAllowExt());
return false;
}
if (!is_uploaded_file($this ->getUploadInfo('tmp_name'))) {
$this ->error('非法上传文件，已删除');
@unlink($this ->getUploadInfo('tmp_name'));
return false;
}
$this ->move();
}
private function move() {
if (!is_dir($this ->upPath)) mkdir($this ->upPath,0755,true);
$this ->setSaveInfo('saveName',$this ->getSaveName());
$this ->setSaveInfo('savePath',$this ->upPath .$this ->getSaveInfo('saveName'));
if (!move_uploaded_file($this ->getUploadInfo('tmp_name'),$this ->getSaveInfo('savePath'))) {
$this ->error('文件无法移动');
return false;
}
$this ->setSaveInfo('fileName',$this ->getUploadInfo('name'));
$this ->setSaveInfo('fileSize',$this ->getUploadInfo('size'));
$this ->setSaveInfo('fileType',$this ->getUploadInfo('type'));
return true;
}
private function chkSize() {
$fileSize = $this ->getUploadInfo('size');
return ($fileSize >0 &&$fileSize <$this ->maxSize) ?true : false;
}
private function chkType() {
return in_array($this ->getExt(),$this ->allowType);
}
private function getExt() {
$extNum = strrpos($this ->getUploadInfo('name'),'.');
if ($extNum === false) return ;
return substr($this ->getUploadInfo('name'),$extNum);
}
public function getAllowExt() {
$allowExt = '';
foreach (array_unique($this ->allowType) as $v) {
$allowExt .= $v .',';
}
return substr($allowExt,0,-1);
}
private function getUploadInfo($var) {
return $this ->uploadInfo[$this ->inputName][$var];
}
private function setSaveInfo($var,$val = '') {
$this ->saveInfo[$this ->inputName][$var] = $val;
}
public function getSaveInfo($var = '',$inputName = '') {
if (empty($inputName)) {
if (empty($var)) return $this ->saveInfo[$this ->inputName];
return $this ->saveInfo[$this ->inputName][$var];
}elseif ($inputName == 'all') return $this ->saveInfo;
if (empty($var)) return $this ->saveInfo[$inputName];
return $this ->saveInfo[$inputName][$var];
}
private function pathFormat($path) {
if (substr($path,-1) != '/') $path .= '/';
return $path;
}
private function byteFormat($size,$dec = 2) {
$a = array("B","KB","MB","GB","TB","PB");
$pos = 0;
while ($size >= 1024) {
$size /= 1024;
$pos++;
}
return round($size,$dec) ." ".$a[$pos];
}
private function getSaveName() {
$saveName = $this ->getUploadInfo('save_name');
if (empty($saveName)) $saveName = $this ->notExistsFileName();
else {
$saveName .= $this ->getExt();
if (!$this ->overWrite) $saveName = $this ->newSaveName($saveName);
}
return $saveName;
}
private function newSaveName($fileName,$newFileName = '',$num = 1) {
if (empty($newFileName)) $newFileName = $fileName;
if (file_exists($this ->upPath .$newFileName)) {
$offset = strrpos($fileName,'.');
$newFileName = substr($fileName,0,$offset) ."($num).".substr($fileName,$offset +1);
return $this ->newSaveName($fileName,$newFileName,$num +1);
}
return $newFileName;
}
private function notExistsFileName() {
$saveName = time() .mt_rand(1000,9999) .'.'.$this ->getExt();
if (file_exists($this ->upPath .$saveName)) $saveName = $this ->notExistsFileName();
return $saveName;
}
private function error($error) {
$this ->setSaveInfo('error',"文件名：{$this->getUploadInfo('name')};错误：{$error}");
}
}
?>