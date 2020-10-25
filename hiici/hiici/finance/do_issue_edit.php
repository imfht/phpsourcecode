<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

global $config;
if (!in_array($auth['id'], $config['manager'])) die('用户权限不够!');

if (empty($_POST['issue_id'])) die;

$issue_id = intval($_POST['issue_id']);
$kind = filter_var($_POST['kind'], FILTER_SANITIZE_STRING);
$title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
$content = cleanjs($_POST['content']);

if (empty($title) || empty($content)) {
	put_info('表单内容不规范！');
	header('Location:?c=finance&a=issue_edit&issue_id='.$issue_id);
	die;
}

$rs = dt_query("UPDATE finance_issue SET title='$title', content='$content', kind='$kind' WHERE id=$issue_id");
if (!$rs) {
	put_info('公告更新失败！');
	header('Location:?c=finance&a=issue_edit&issue_id='.$issue_id);
	die;
}

put_info('公告更新成功！');
header('Location:?c=finance&a=issue_manage');
die;
