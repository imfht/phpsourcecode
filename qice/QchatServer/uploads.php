<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");

@set_time_limit(60);

$rootDir = './uploads';
if(!file_exists($rootDir)) {
  @mkdir($rootDir);
}

$targetDir = $rootDir.'/'.date('ymd');
if (!file_exists($targetDir)) {
  @mkdir($targetDir);
}

if ( empty($_FILES) ) {
  echo json_encode(['status' => 0, 'msg' => '没找到文件']);
  exit;
}

if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
  echo json_encode(['status' => 0, 'msg' => '上传文件出错']);
  exit;
}

if ( $_FILES['file']['size'] > 2*1024*1024 ) {
  echo json_encode(['status' => 0, 'msg' => '文件不能超过2MB']);
  exit;
}

$fileName = $_FILES["file"]["name"];
$suffix   = strtolower(array_pop(explode('.',$_FILES["file"]["name"])));

if ( !in_array($suffix, ['jpg', 'jpeg', 'bmp', 'gif', 'png', 'zip']) ) {
  echo json_encode(['status' => 0, 'msg' => '只能上传图片或ZIP包']);
  exit;
}

$newFile = $targetDir.DIRECTORY_SEPARATOR.md5(uniqid()).'.'.$suffix;
move_uploaded_file($_FILES["file"]["tmp_name"], $newFile);

$arr = explode('/', $newFile);
$name = $arr[count($arr)-2]."/".$arr[count($arr)-1];

$json = ['status' => 1, "url"=>"http://". $_SERVER['HTTP_HOST'] ."/uploads/".$name];
echo json_encode($json);
exit;
