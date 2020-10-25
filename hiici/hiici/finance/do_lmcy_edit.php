<?php 

$auth = $_SESSION['auth'];
if (empty($auth)) die('用户未登录！');

if (empty($_POST['lmcy_id'])) die;

$lmcy_id = intval($_POST['lmcy_id']);

global $config;
if (!in_array($auth['id'], $config['manager'])) if (1 > dt_count('finance_lmcy', 'WHERE id = '.$lmcy_id.' AND user_id = '.$auth['id'])) die('用户权限不够!^_^');

$kind = filter_var($_POST['kind'], FILTER_SANITIZE_STRING);
$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
$user_id = intval($_POST['user_id']);

if (empty($name) || empty($kind)) {
	put_info('表单内容不规范！');
	header('Location:?c=finance&a=lmcy_edit&lmcy_id='.$lmcy_id);
	die;
}

if (1 > dt_count('finance_lmcy', 'WHERE id = '.$lmcy_id.' AND user_id ='.$user_id) && 0 < dt_count('finance_lmcy', 'WHERE user_id ='.$user_id)) {
	put_info('重复的用户ID！^_^');
	header('Location:?c=finance&a=lmcy_edit&lmcy_id='.$lmcy_id);
	die;
}

$rs = dt_query("UPDATE finance_lmcy SET name='$name', user_id='$user_id', kind='$kind' WHERE id=$lmcy_id");
if (!$rs) {
	put_info('成员更新失败！');
	header('Location:?c=finance&a=lmcy_edit&lmcy_id='.$lmcy_id);
	die;
}

$logo_f = $_FILES['logo'];
if (!empty($logo_f['name'])) {
	$logo_f['name'] = null;
	if (!do_upload_file($logo_f, 'img/finance/lmcy/logo/'.$lmcy_id, array('image/png', 'image/gif', 'image/jpeg', 'image/pjpeg'), 1000000, true)) {
		put_info('图标上传失败!^_^'); 
		header('Location:?c=finance&a=lmcy_edit&lmcy_id='.$lmcy_id);
		die;
	}
}

$h_img_f = $_FILES['h_img'];
if (!empty($h_img_f['name'])) {
	$h_img_f['name'] = null;
	if (!do_upload_file($h_img_f, 'img/finance/lmcy/h_img/'.$lmcy_id, array('image/png', 'image/gif', 'image/jpeg', 'image/pjpeg'), 1000000, true)) {
		put_info('主图上传失败!^_^'); 
		header('Location:?c=finance&a=lmcy_edit&lmcy_id='.$lmcy_id);
		die;
	}
}

put_info('成员编辑成功！^_^');
(in_array($auth['id'], $config['manager'])) ? header('Location:?c=finance&a=lmcy_manage') : header('Location:?c=finance&a=item_manage');
die;
