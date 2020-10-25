<?php
/**
 * 文章内容页
 */
require './includes/init.php';

$navModel = new ColumnModel();
$contentModel = new ContentModel();
$commentModel = new CommentModel();


$id = intval($_GET['id']) ?  intval($_GET['id']) : 0 ;
$article = $contentModel->oneContent($id);

//文章点击阅读数加一
$contentModel->addClick($id);

$article = $article[0];

//下一篇文章
$next = $contentModel->showNextList($id,$article['column_id']);
$next_id = $next[0]['id'];
$next_title = $next[0]['title'];

//上一篇文章
$prev = $contentModel->showPrevList($id,$article['column_id']);
$prev_id = $prev[0]['id'];
$prev_title = $prev[0]['title'];


//获取文章的评论列表
$comment_list = $commentModel->CommentList($id);

//热点推荐
$hot = $contentModel->showHotList($article['column_id']);

//相关推荐
$rec = $contentModel->showRecommendList($article['column_id']);


//导航栏
$nav = $navModel->showNav();

include TEMP_DIR . 'article.html';