<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_POST)) die;

$content = cleanjs($_POST['content']);

if (empty($content)) die(json_encode(array('msg' => '空的内容！', 'token' => get_token())));

if (!shuo_add($content, $auth['id'], $auth['name'])) die('发布说说失败！');

die(json_encode(array('msg' => 's0', 'token' => get_token())));
