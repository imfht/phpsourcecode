<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

$face_f = $_FILES['face'];
$face_url = 'img/center/user_face/'.$auth['id'].'.jpg';
$face_min_url = 'img/center/user_face/'.$auth['id'].'_min.jpg';

if (!in_array($face_f['type'], array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png')) || $face_f['size'] > 10000000) die('违规文件！');

require_once('inc/lib/tsImage.php');
new tsImage($face_f['tmp_name'], 220, 220, true,  $face_url);
new tsImage($face_f['tmp_name'], 70, 70, true,  $face_min_url);

global $config;
if ($config['OSS_ACCESS_ID']) require_once('inc/umeditor/php/oss.php');  //如果开启了阿里OSS服务

die('s0');
