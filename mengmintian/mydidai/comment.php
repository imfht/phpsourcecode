<?php
require './includes/init.php';

$navModel = new ColumnModel();
$contentModel = new ContentModel();
$commentModel = new CommentModel();

//发布评论
if($_POST['action'] == 'post'){
	$aid = intval($_POST['aid']);
	$content = $_POST['content'];
	$uid = 1;
	$commentModel->PostComment($aid,$content,$uid);
	//header("location:".$_SERVER['']);
}

//ajax获取评论列表
if($_GET['action'] == 'ajax_list'){
	$aid = intval($_GET['aid']);
	$start = intval($_GET['s']);
	$start = $s * 15;
	$ajax_list = $commentModel->CommentList($aid);
}