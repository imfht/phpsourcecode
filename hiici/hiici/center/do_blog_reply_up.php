<?php 

$auth = @$_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

if (empty($_GET['blog_reply_id'])) die;

$blog_reply_id = intval($_GET['blog_reply_id']);

if (0 < dt_count('blog_reply_up', "WHERE blog_reply_id = $blog_reply_id AND user_id = ".$auth['id'])) die('已经赞过了！^_^');

$rs = dt_query("INSERT INTO blog_reply_up (blog_reply_id, user_id, c_at) VALUES ($blog_reply_id, ".$auth['id'].", ".time().")"); 
if (!$rs) die('博客回复赞数据变更失败!');

$rs = dt_query("UPDATE blog_reply SET up_c = up_c + 1 WHERE id = $blog_reply_id");
if (!$rs) die('更新博客回复赞统计失败！');

$blog_reply = dt_query_one("SELECT user_id, blog_id FROM blog_reply WHERE id = $blog_reply_id");
if (!$blog_reply) die('获取博客回复数据失败！');
$blog = dt_query_one("SELECT title, user_name FROM blog WHERE id = ".$blog_reply['blog_id']);
if (!$blog) die('获取博客数据失败！');
if (!msg_sys_add('[<b>'.$auth['name'].'</b>] 在 [<b>'.$blog['user_name'].'</b>] 的日志 <a href="?c=center&blog_id='.$blog_reply['blog_id'].'">'.get_substr($blog['title']).'</a> 里为您的回复点了赞', $blog_reply['user_id'])) die('系统信息发送失败！');

die('s0');
