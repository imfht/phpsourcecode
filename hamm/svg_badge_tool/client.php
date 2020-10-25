<?php
require_once('common.php');
if(empty($_GET['type'])){
    header('Location: https://gitee.com/hamm/svg_badge_tool');
    die;
}
$type = trim($_GET['type'] ?? 'star');
$key="Client";
$value="Tools";
switch($type){
    case 'ip':
        $key = "IP";
        $value=get_client_ip();
        break;
    case 'os':
        $key = "系统";
        $value=getOs();
        break;
    case 'broswer':
        $key = "浏览器";
        $value=getBrowser();
        break;
    default:
}
require_once('svg.php');
?>