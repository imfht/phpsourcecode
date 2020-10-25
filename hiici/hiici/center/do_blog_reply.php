<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_POST['blog_id'])) die;

$blog_id = intval($_POST['blog_id']);
$to_user_id = intval($_POST['to_user_id']);
$content = cleanjs($_POST['content']);

if (empty($content)) die(json_encode(array('msg' => '空的内容！^_^', 'token' => get_token())));

$rs = dt_query("INSERT INTO blog_reply (content, blog_id, user_id, user_name, c_at) 
	VALUES ('$content', $blog_id, ".$auth['id'].", '".$auth['name']."', ".time().")"); 
if (!$rs) die('博客回复数据变更失败!');

if (empty($to_user_id)) {
	$blog = dt_query_one("SELECT title, user_id FROM blog WHERE id = $blog_id");
	if (!$blog) die('获取日志数据失败！');
	if (!msg_sys_add('[<b>'.$auth['name'].'</b>] 为您的日志 <a href="?c=center&blog_id='.$blog_id.'">'.get_substr($blog['title']).'</a> 添加了新的回复', $blog['user_id'])) die('系统信息发送失败！');
} else {
	$blog = dt_query_one("SELECT title, user_name FROM blog WHERE id = $blog_id");
	if (!$blog) die('获取日志数据失败！');
	if (!msg_sys_add('[<b>'.$auth['name'].'</b>] 在 [<b>'.$blog['user_name'].'</b>] 的日志 <a href="?c=center&blog_id='.$blog_id.'">'.get_substr($blog['title']).'</a> 里为您的回复添加了新的回复', $to_user_id)) die('系统信息发送失败！');
}

die(json_encode(array('msg' => 's0', 'token' => get_token())));
