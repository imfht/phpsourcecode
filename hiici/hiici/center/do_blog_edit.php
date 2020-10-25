<?php

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_POST['blog_id'])) die;

$blog_id = intval($_POST['blog_id']);
$title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
$content = cleanjs($_POST['content']);

if (empty($title) || empty($content)) die('空的标题或内容！');

$cond = "WHERE id = $blog_id AND user_id = ".$auth['id'];

if (1 > dt_count('blog', $cond)) die('违规编辑非属博客！');

$rs = dt_query("UPDATE blog SET title = '$title', content = '$content' $cond");
if (!$rs) die('编辑博客失败！');

die('s0');
