<?php
require './includes/init.php';

$navModel = new ColumnModel();
$contentModel = new ContentModel();


$nav = $navModel->showNav();

$id = intval($_GET['id']) ? intval($_GET['id']) : 0;
if ($id) {
    
    $list = $contentModel->showList($id);
    $page_title = column_name($id);
} else {
    $list = $contentModel->showAllList();
    
    $page_title = '全部列表';
}

$pageClass = new PageTool(100,10);
$page = $pageClass->show(4);

//热点推荐
$hot = $contentModel->showHotList($id);

//相关推荐
$rec = $contentModel->showRecommendList($id);

include TEMP_DIR . 'list.html';

