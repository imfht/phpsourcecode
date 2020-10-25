<?php
require './includes/init.php';


//模型实例化
$flinkModel = new FriendlinkModel();
$navModel = new ColumnModel();
$contentModel = new ContentModel();
$tagModel = new TagModel();

//导航栏
$nav = $navModel->showNav();

//友情链接
$textflink = $flinkModel->textFriendlink();
$picflink = $flinkModel->picFriendlink();

//推荐图书
$recbook = $contentModel->RecBook();

//最新新闻
$newnews = $contentModel->NewNews();
//头条
$onetop = $contentModel->OneTopNews();
//推荐图文
$recpictext = $contentModel->RecPicText();

//获取网络资源导航
$netnav = $navModel->NetNav();

//标签
$tag = $tagModel->IndexTag();

include TEMP_DIR . 'index.html';
?>        
