<?php
if(empty($_GET['key']) || empty($_GET['value'])){
    header('Location: https://gitee.com/hamm/svg_badge_tool');
    return;
}
$key = $_GET['key'];
$value = $_GET['value'];
header("Location: https://svg.hamm.cn/badge.svg?key={$key}&value={$value}");
?>