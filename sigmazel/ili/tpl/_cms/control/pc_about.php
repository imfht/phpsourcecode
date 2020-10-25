<?php
/**@name 团队*/

//分类备注
$categories['06']['COMMENT'] = explode("\r\n", $categories['06']['COMMENT']);

//介绍
$article_061 = $_article->get_first('061', '', '', 1);

//团队成员
$article_062 = $_article->get_first('062', '', '', 1);

//联系我们
$article_063 = $_article->get_first('063', '', '', 1);

//当前导航
$menu['about'] = 'nav-active';
$page_title = "团队 - {$page_title}";

include_once view('/tpl/_cms/view/pc_about');
?>