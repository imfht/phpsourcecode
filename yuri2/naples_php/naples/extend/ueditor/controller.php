<?php
//header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
//header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
use \naples\app\SysNaples\controller\Ueditor;
date_default_timezone_set("Asia/Chongqing");
error_reporting(E_ERROR);
header("Content-Type: text/html; charset=utf-8");
$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(Ueditor::DIR_UE."/config.json")), true);
$CONFIG['imageUrlPrefix']=UEDITOR_PREFIX;$CONFIG['imagePathFormat']=UEDITOR_UPLOAD_PATH.'/image/{yyyy}{mm}{dd}/{time}{rand:6}';
$CONFIG['scrawlUrlPrefix']=UEDITOR_PREFIX;$CONFIG['scrawlPathFormat']=UEDITOR_UPLOAD_PATH.'/scraw/{yyyy}{mm}{dd}/{time}{rand:6}';
$CONFIG['snapscreenUrlPrefix']=UEDITOR_PREFIX;$CONFIG['snapscreenPathFormat']=UEDITOR_UPLOAD_PATH.'/snapscreen/{yyyy}{mm}{dd}/{time}{rand:6}';
$CONFIG['catcherUrlPrefix']=UEDITOR_PREFIX;$CONFIG['catcherPathFormat']=UEDITOR_UPLOAD_PATH.'/catcher/{yyyy}{mm}{dd}/{time}{rand:6}';
$CONFIG['videoUrlPrefix']=UEDITOR_PREFIX;$CONFIG['videoPathFormat']=UEDITOR_UPLOAD_PATH.'/video/{yyyy}{mm}{dd}/{time}{rand:6}';
$CONFIG['fileUrlPrefix']=UEDITOR_PREFIX;$CONFIG['filePathFormat']=UEDITOR_UPLOAD_PATH.'/file/{yyyy}{mm}{dd}/{time}{rand:6}';
$CONFIG['imageManagerUrlPrefix']=Yuri2::getHttpType().'://'.Yuri2::getHost();$CONFIG['imageManagerListPath']=UEDITOR_UPLOAD_PATH.'';
$CONFIG['fileManagerUrlPrefix']=Yuri2::getHttpType().'://'.Yuri2::getHost();$CONFIG['fileManagerListPath']=UEDITOR_UPLOAD_PATH.'/file';
$action = isset($_GET['action'])?$_GET['action']:'none';

switch ($action) {
    case 'config':
        $result =  json_encode($CONFIG);
        break;

    /* 上传图片 */
    case 'uploadimage':
    /* 上传涂鸦 */
    case 'uploadscrawl':
    /* 上传视频 */
    case 'uploadvideo':
    /* 上传文件 */
    case 'uploadfile':
        $result = include(Ueditor::DIR_UE."/action_upload.php");
        break;

    /* 列出图片 */
    case 'listimage':
        $result = include(Ueditor::DIR_UE."/action_list.php");
        break;
    /* 列出文件 */
    case 'listfile':
        $result = include(Ueditor::DIR_UE."/action_list.php");
        break;

    /* 抓取远程文件 */
    case 'catchimage':
        $result = include(Ueditor::DIR_UE."/action_crawler.php");
        break;

    default:
        $result = json_encode(array(
            'state'=> '请求地址出错'
        ));
        break;
}

/* 输出结果 */
if (isset($_GET["callback"])) {
    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
    } else {
        echo json_encode(array(
            'state'=> 'callback参数不合法'
        ));

    }
} else {
    echo $result;
}