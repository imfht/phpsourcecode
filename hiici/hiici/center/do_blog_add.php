<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_POST)) die;

$title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
$content = cleanjs($_POST['content']);

if (empty($title) || empty($content)) die('空的标题或内容！');

$rs = dt_query("INSERT INTO blog (title, content, user_id, user_name, c_at) VALUES ('$title', '$content', ".$auth['id'].", '".$auth['name']."',".time().")");
if (!$rs) die('发布博客失败！');

//在空间自动发布说说
$blog_id = dt_query_one("SELECT LAST_INSERT_ID()")[0];
$shuo_content = '<p>发布了日志 <a href="?c=center&blog_id='.$blog_id.'">'.get_substr($title, 20).'</a></p>'; 
if (!shuo_add($shuo_content, $auth['id'], $auth['name'])) die('在do_blog_add发布说说失败！^_^');

die('s0');
