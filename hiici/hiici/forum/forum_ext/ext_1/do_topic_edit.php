<?php

$price = doubleval($_POST['price']);
$price_org = doubleval($_POST['price_org']);
$phone = @filter_var($_POST['phone'], FILTER_SANITIZE_STRING);

$rs = dt_query("UPDATE forum_topic_ext_1 SET price = '$price', price_org = '$price_org', phone = '$phone' WHERE id = $topic_id");
if (!$rs) die('更新forum_topic_ext_1数据失败！^_^');
