<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_GET['email'])) die('空的内容！^_^');

$email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
$email_code = $_GET['email_code'];

if ($email_code != $_SESSION['email_code'] || $email != $_SESSION['email']) die('邮箱激活码不正确！^_^');

$rs = dt_query("UPDATE user SET email = '$email' WHERE id = ".$auth['id']);
if (!$rs) die('数据变更失败！');

die('s0');
