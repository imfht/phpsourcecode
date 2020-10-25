<?php
checkUser('cms_login.php'); // 判断登陆
$cids = checkAdminPriv(@$privilege); // 判断权限
$dataops = new Dataops(); // 实例
?>
