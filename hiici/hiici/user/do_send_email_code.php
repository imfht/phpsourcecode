<?php

if (!empty($_POST)) die;

if(@$_SESSION['email_code_send_time'] > time()) die('获取的太过频繁！^_^');
if(!empty($_SESSION['auth']) && is_qq_user($_SESSION['auth']['id'])) die('您是QQ用户！^_^');

$email = @filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);

if (!is_email($email)) die('邮箱格式不正确！^_^');
if (dt_query_one("SELECT id from user WHERE email = '$email' LIMIT 1")) die('邮箱已经被其他用户注册！^_^');

$_SESSION['email'] = $email;
$_SESSION['email_code'] = md5(rand());
$_SESSION['email_code_send_time'] = time()+60;
if (!send_email($email, '【'.SYS_NAME.'】邮箱激活码。', '您本次获取的恒信网激活码为：'.$_SESSION['email_code'])) die('发送失败！^_^');

die('s0');
