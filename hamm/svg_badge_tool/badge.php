<?php
require_once('common.php');
if(empty($_GET['key']) || empty($_GET['value'])){
    header('Location: https://gitee.com/hamm/svg_badge_tool');
    die;
}
$key = trim($_GET['key'] ?? 'Key');
$value = trim($_GET['value'] ?? 'Value');
require_once('svg.php');
?>