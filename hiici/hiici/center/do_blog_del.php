<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_GET['blog_id'])) die;

$blog_id = intval($_GET['blog_id']);

$cond = "WHERE id = $blog_id AND user_id = ".$auth['id'];

if (1 > dt_count('blog', $cond)) die('违规删除非属博客！');

$rs = dt_query("DELETE FROM blog $cond"); 
if (!$rs) die('博客数据变更失败!');
$rs = dt_query("DELETE FROM blog_up WHERE blog_id = $blog_id"); 
if (!$rs) die('博客赞数据变更失败!');
$rs = dt_query("DELETE FROM blog_reply_up WHERE blog_reply_id in (SELECT id FROM blog_reply WHERE blog_id = $blog_id)"); 
if (!$rs) die('博客回复赞数据变更失败!');
$rs = dt_query("DELETE FROM blog_reply WHERE blog_id = $blog_id"); 
if (!$rs) die('博客回复数据变更失败!');

die('s0');
