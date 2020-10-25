<?php 

$auth = @$_SESSION['auth'];
if (empty($auth)) die('用户未登录！^_^');

if (empty($_GET['blog_id'])) die;

$blog_id = intval($_GET['blog_id']);

if (0 < dt_count('blog_up', "WHERE blog_id = $blog_id AND user_id = ".$auth['id'])) die('已经赞过了！^_^');

$rs = dt_query("INSERT INTO blog_up (blog_id, user_id, c_at) VALUES ($blog_id, ".$auth['id'].", ".time().")"); 
if (!$rs) die('博客赞数据变更失败!');

$rs = dt_query("UPDATE blog SET up_c = up_c + 1 WHERE id = $blog_id");
if (!$rs) die('更新博客赞统计失败！');

$blog = dt_query_one("SELECT title, user_id FROM blog WHERE id = $blog_id");
if (!$blog) die('获取说说数据失败！');
if (!msg_sys_add('[<b>'.$auth['name'].'</b>] 为您的日志 <a href="?c=center&blog_id='.$blog_id.'">'.get_substr($blog['title']).'</a> 点了赞', $blog['user_id'])) die('系统信息发送失败！');

die('s0');
