<?php

$price = doubleval($_POST['price']);
$price_org = doubleval($_POST['price_org']);
$phone = @filter_var($_POST['phone'], FILTER_SANITIZE_STRING);

$rs = dt_query("INSERT INTO forum_topic_ext_1 (id, price, price_org, phone) VALUES (last_insert_id(), '$price', '$price_org', '$phone')");
if (!$rs) die('新建forum_topic_ext_1数据失败！^_^');
