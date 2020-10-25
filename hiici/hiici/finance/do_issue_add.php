<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

global $config;
if (!in_array($auth['id'], $config['manager'])) die('用户权限不够!');

if (empty($_POST['kind'])) die;

$kind = filter_var($_POST['kind'], FILTER_SANITIZE_STRING);
$title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
$content = cleanjs($_POST['content']);
$content_s = mb_substr(filter_var($content, FILTER_SANITIZE_STRING), 0, 200, SYS_ENCODE);

if (empty($title) || empty($content)) {
	put_info('表单内容不规范！');
	header('Location:?c=finance&a=issue_add');
	die;
}

$rs = dt_query("INSERT INTO finance_issue (title, content, content_s, kind, c_at) 
	VALUES ('$title', '$content', '$content_s', '$kind',".time().")");
if (!$rs) {
	put_info('公告发布失败！');
	header('Location:?c=finance&a=issue_add');
	die;
}


// 清空cookie
foreach (array_keys($_POST) as $n) {
	setcookie('issue_add_'.$n, '', time()-3600, '/');
}

put_info('公告发布成功！');
header('Location:?c=finance&a=issue_manage');
die;

