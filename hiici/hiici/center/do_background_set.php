<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_GET['background'])) die('空的内容！^_^');

$background = do_img_url_filter($_GET['background']);

$rs = dt_query("UPDATE user_info SET background_url = '$background' WHERE id = ".$auth['id']);
if (!$rs) die('数据变更失败！');

die('s0');
