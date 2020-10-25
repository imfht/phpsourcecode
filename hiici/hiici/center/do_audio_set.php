<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_GET['audio'])) die('空的内容！^_^');

$audio = filter_var($_GET['audio'], FILTER_SANITIZE_URL);

$rs = dt_query("UPDATE user_info SET audio_url = '$audio' WHERE id = ".$auth['id']);
if (!$rs) die('数据变更失败！');

die('s0');
