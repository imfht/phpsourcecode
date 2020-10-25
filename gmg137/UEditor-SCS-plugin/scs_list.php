<?php
/**
 * 获取已上传的文件列表
 * User: gmg137
 * Date: 14-09-05
 * Time: 上午11:21
 */
include "scs_Uploader.class.php";

/* 判断类型 */
switch ($_GET['action']) {
    /* 列出文件 */
    case 'listfile':
        $allowFiles = $CONFIG['fileManagerAllowFiles'];
        $listSize = $CONFIG['fileManagerListSize'];
        $path = $CONFIG['fileManagerListPath'];
        break;
    /* 列出图片 */
    case 'listimage':
    default:
        $allowFiles = $CONFIG['imageManagerAllowFiles'];
        $listSize = $CONFIG['imageManagerListSize'];
        $path = $CONFIG['imageManagerListPath'];
}
$allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

/* 获取参数 */
$size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
$start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
$end = $start + $size;

/* 获取文件列表 */
$scs = new SCS($accessKey,$secretKey);  //初始化新浪SCS
$get_filelist = SCS::getBucket($bucket);    //获取文件列表
foreach($get_filelist as $value){
    if($_GET['action'] == 'listimage'){
        preg_match("/^upload\/image\/.*[$allowFiles]$/",$value['name'],$urls);
    }else if($_GET['action'] == 'listfile'){
        preg_match("/^upload\/file\/.*[$allowFiles]$/",$value['name'],$urls);
    }
    if($urls){
        $files[] = array('url'=>$bucket . "/" . $urls[0],'mtime'=>$value['time']);
    }
}

if (!count($files)) {
    return json_encode(array(
        "state" => "no match file",
        "list" => array(),
        "start" => $start,
        "total" => count($files)
    ));
}

/* 获取指定范围的列表 */
$len = count($files);
for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
    $list[] = $files[$i];
}
//倒序
//for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
//    $list[] = $files[$i];
//}

/* 返回数据 */
$result = json_encode(array(
    "state" => "SUCCESS",
    "list" => $list,
    "start" => $start,
    "total" => count($files)
));

return $result;
