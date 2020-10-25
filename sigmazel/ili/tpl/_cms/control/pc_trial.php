<?php
/**@name 实验室*/

//分类备注
$categories['041']['COMMENT'] = explode("\r\n", $categories['041']['COMMENT']);

//一拍即合限6个
$article_041 = $_article->get_list('041', 0, 6, '', '', 1);

//微卡限6个
$article_042 = $_article->get_list('042', 0, 6, '', '', 1);

//当前导航
$menu['trial'] = 'nav-active';
$page_title = "实验室 - {$page_title}";

include_once view('/tpl/_cms/view/pc_trial');
?>